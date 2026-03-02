FROM node:23.7.0-alpine3.20
RUN deluser --remove-home node && \
    addgroup -g 1000 devteam && \
    adduser -u 1000 -G devteam -s /bin/sh -D emilio
WORKDIR /app
RUN chown emilio:devteam /app
USER emilio
COPY --chown=emilio:devteam package*.json ./
RUN npm install
COPY --chown=emilio:devteam . .
EXPOSE 5173
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]