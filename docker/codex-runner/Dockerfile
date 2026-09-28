FROM node:22-alpine
RUN npm install -g @openai/codex
WORKDIR /correction-workspaces
COPY entrypoint.sh /usr/local/bin/codex-runner
RUN chmod 0755 /usr/local/bin/codex-runner

ENTRYPOINT ["/usr/local/bin/codex-runner"]
