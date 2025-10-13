package main

import (
	"bytes"
	"context"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	nurl "net/url"
	"strings"

	readability "github.com/go-shiori/go-readability"
	"github.com/spf13/cobra"
	"go.opentelemetry.io/contrib/instrumentation/net/http/otelhttp"
	"go.opentelemetry.io/otel"
	"go.opentelemetry.io/otel/exporters/otlp/otlptrace"
	"go.opentelemetry.io/otel/exporters/otlp/otlptrace/otlptracehttp"
	"go.opentelemetry.io/otel/propagation"
	"go.opentelemetry.io/otel/sdk/trace"
)

const index = `<!DOCTYPE HTML>
<html>
 <head>
  <meta charset="utf-8">
  <title>go-readability</title>
 </head>
 <body>
 <form action="/" style="width:80%">
  <fieldset>
   <legend>Get readability content</legend>
   <p><label for="url">URL </label><input type="url" name="url" style="width:90%"></p>
   <p><input type="checkbox" name="metadata" value="true">only get the page's metadata</p>
  </fieldset>
  <p><input type="submit"></p>
 </form>
 </body>
</html>`

func main() {
	ctx := context.Background()
	client := otlptracehttp.NewClient()
	traceExporter, err := otlptrace.New(ctx, client)

	if err != nil {
		log.Fatalf("failed to initialize exporter: %e", err)
	}
	tp := trace.NewTracerProvider(
		trace.WithBatcher(traceExporter),
	)
	otel.SetTracerProvider(tp)
	otel.SetTextMapPropagator(propagation.TraceContext{})

	rootCmd := &cobra.Command{
		Use:   "go-readability [flags] [source]",
		Run:   rootCmdHandler,
		Short: "go-readability is parser to fetch readable content of a web page",
		Long: "go-readability is parser to fetch the readable content of a web page.\n" +
			"The source can be an url or an existing file in your storage.",
	}

	rootCmd.Flags().StringP("http", "l", "", "start the http server at the specified address")
	errFlag := rootCmd.MarkFlagRequired("http")

	if errFlag != nil {
		panic(errFlag)
	}

	err = rootCmd.Execute()
	if err != nil {
		log.Fatalln(err)
	}
}

func rootCmdHandler(cmd *cobra.Command, args []string) {
	// Start HTTP server
	httpListen, _ := cmd.Flags().GetString("http")
	if httpListen != "" {
		http.Handle("/", otelhttp.NewHandler(http.HandlerFunc(httpHandler), "Main"))
		log.Fatal(http.ListenAndServe(httpListen, nil))
	}

	if len(args) == 0 {
		cmd.Help()
	}
}

func httpHandler(w http.ResponseWriter, r *http.Request) {
	url := r.URL.Query().Get("url")
	if url == "" {
		w.Write([]byte(index))
	} else {
		log.Println("process URL", url)
		content, err := getContent(url)
		if err != nil {
			log.Println(err)
			http.Error(w, err.Error(), http.StatusBadRequest)
			return
		}
		w.Header().Set("Content-Type", "application/json")
		w.Write([]byte(content))
	}
}

func getContent(srcPath string) (string, error) {
	var (
		pageURL   *nurl.URL
		srcReader io.Reader
	)

	url, isURL := validateURL(srcPath)

	if !isURL {
		return "", fmt.Errorf("url %s does not look like valid url", url)
	}

	resp, err := http.Get(srcPath)
	if err != nil {
		return "", fmt.Errorf("failed to fetch web page: %v", err)
	}
	defer resp.Body.Close()

	pageURL = url
	srcReader = resp.Body

	// Use tee so the reader can be used twice
	buf := bytes.NewBuffer(nil)
	tee := io.TeeReader(srcReader, buf)

	// Make sure the page is readable
	if !readability.Check(tee) {
		return "", fmt.Errorf("failed to parse page: the page is not readable")
	}

	// Get readable content from the reader
	article, err := readability.FromReader(buf, pageURL)
	if err != nil {
		return "", fmt.Errorf("failed to parse page: %v", err)
	}

	metadata := map[string]interface{}{
		"title":   article.Title,
		"content": article.Content,
	}

	prettyJSON, err := json.MarshalIndent(&metadata, "", "    ")
	if err != nil {
		return "", fmt.Errorf("failed to write metadata file: %v", err)
	}

	return string(prettyJSON), nil
}

func validateURL(path string) (*nurl.URL, bool) {
	url, err := nurl.ParseRequestURI(path)
	return url, err == nil && strings.HasPrefix(url.Scheme, "http")
}
