
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    python3 \
    ffmpeg \
    curl \
    ca-certificates \
    && curl -fL https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp \
       -o /usr/local/bin/yt-dlp \
    && chmod 755 /usr/local/bin/yt-dlp \
    && rm -rf /var/lib/apt/lists/*

# Verify installations during build
RUN yt-dlp --version && ffmpeg -version

WORKDIR /var/www/html

COPY . .

# Start PHP server on Render's assigned port
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000}"]
