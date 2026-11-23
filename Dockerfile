FROM php:8.2-cli

# Install dependencies (python3, ffmpeg, curl) and Linux yt-dlp
RUN apt-get update && apt-get install -y \
    python3 \
    ffmpeg \
    curl \
    && curl -L https://github.com -o /usr/local/bin/yt-dlp \
    && chmod a+rx /usr/local/bin/yt-dlp \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . .

# Bind to 0.0.0.0 and Render's dynamic PORT (defaults to 10000)
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000}"]
