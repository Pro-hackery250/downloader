# TODO - Video Preview Card Feature

## Task: Develop a YouTube video preview card feature

### Steps to Complete:

- [x] 1. Add metadata API endpoint in download.php
  - Create endpoint to fetch video info using yt-dlp
  - Return JSON: title, thumbnail, channel, duration, views, upload date

- [ ] 2. Update index.js with paste detection and preview card
  - Add paste event listener for YouTube URL detection
  - Add regex validation for YouTube URLs
  - Create function to call backend metadata API
  - Implement video preview card display function

- [ ] 3. Add CSS styles for video preview card
  - Layout: LEFT (thumbnail + duration) | RIGHT (title, channel, stats)
  - Download buttons styling below the card

- [ ] 4. Test the implementation

