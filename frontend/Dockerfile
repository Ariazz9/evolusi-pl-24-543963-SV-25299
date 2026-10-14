# =========================
# Stage 1: Build Frontend
# =========================
FROM node:22-alpine AS builder

WORKDIR /app

# Copy dependency definitions first to leverage Docker layer caching
COPY package.json package-lock.json ./

# Install dependencies deterministically
RUN npm ci

# Copy application source code
COPY . .

# Argument for API URL during build time
ARG VITE_API_URL=http://localhost:8000/api
ENV VITE_API_URL=${VITE_API_URL}

# Build production bundle
RUN npm run build

# =========================
# Stage 2: Serve with Nginx
# =========================
FROM nginx:1.27-alpine AS runtime

# Copy custom Nginx configuration for Vue SPA
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copy build output from Stage 1
COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

