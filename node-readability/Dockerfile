FROM node:19.8-slim as build

COPY . /app
WORKDIR /app
RUN npm ci
RUN npx tsc
RUN npm install --omit=dev

FROM node:19.8-slim
COPY --from=build /app/dist /app/
COPY --from=build /app/node_modules /app/node_modules/
CMD [ "node", "/app/microservice.js" ]
