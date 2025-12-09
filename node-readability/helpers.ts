export const removeCssInlineAndExternalLinks = (body: string) => body.replace(/<style[\s\S]*?<\/style>/gi, '')
    .replace(/<link[^>]+stylesheet[^>]*>/gi, '');
