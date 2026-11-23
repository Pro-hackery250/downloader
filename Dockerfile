FROM php:8.2-cli

# yt-dlp needs a JavaScript runtime to solve YouTube's current player challenges.
# Deno is the runtime recommended by yt-dlp.
ENV DENO_INSTALL=/usr/local

RUN apt-get update && apt-get install -y --no-install-recommends \
    python3 \
    ffmpeg \
    curl \
    ca-certificates \
    unzip \
    && curl -fsSL https://deno.land/install.sh | sh \
    && curl -fL https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp \
       -o /usr/local/bin/yt-dlp \
    && chmod 755 /usr/local/bin/yt-dlp \
    && deno --version \
    && yt-dlp --version \
    && ffmpeg -version \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

# Start PHP server on Render's assigned port
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000}"]
