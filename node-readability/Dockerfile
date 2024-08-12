FROM node:22.6-slim as build

COPY . /app
WORKDIR /app
RUN npm ci
RUN npx tsc
RUN npm install --omit=dev

FROM node:22.6-slim
COPY --from=build /app/dist /app/
COPY --from=build /app/instrument.mjs /app/instrument.mjs
COPY --from=build /app/node_modules /app/node_modules/
CMD [ "node", "--import", "/app/instrument.mjs",  "/app/microservice.js" ]
