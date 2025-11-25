// Replace with your YouTube API Key
const API_KEY = "AIzaSyCWyL20troldScmfg0Gp0bLqdaxE3WmLMQ";

document.addEventListener("DOMContentLoaded", () => {
  const searchBtn = document.querySelector(".searc");
  const input = document.querySelector(".inpu");
  const resultDiv = document.querySelector(".result");

  searchBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    const query = input.value.trim();
    if (!query) return;

    // Show loader
    showLoader();

    try {
      const videos = await searchYouTube(query);

      // Keep loader for 2s before showing results
      setTimeout(() => {
        displayVideos(videos);
      }, 2000);
    } catch (error) {
      console.error(error);
      resultDiv.innerHTML = `<p style="color:red;">Error fetching videos</p>`;
    }
  });

  // Search YouTube for videos
  async function searchYouTube(query) {
    const response = await fetch(
      `https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=9&q=${encodeURIComponent(
        query
      )}&key=${API_KEY}`
    );
    const data = await response.json();
    if (!data.items || data.items.length === 0) throw new Error("No results found");
    return data.items;
  }

  // Loader UI
  function showLoader() {
    resultDiv.innerHTML = `
      <div class="loader-container">
        <div class="loader"></div>
      </div>
    `;
  }

  // Display results grid
  function displayVideos(videos) {
    resultDiv.innerHTML = `
      <div style="
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
      ">
        ${videos
          .map((video) => {
            const { title, thumbnails, channelTitle, publishedAt } = video.snippet;
            const videoId = video.id.videoId;
            return `
              <div style="border:1px solid #ddd; border-radius:10px; padding:10px; background:#fff;">
                <img src="${thumbnails.high.url}" alt="${title}" style="width:100%; border-radius:10px;">
                <h4 style="margin:10px 0;">${title}</h4>
                <p style="text-align:left;"><b>Channel:</b> ${channelTitle}</p>
                <p style="text-align:left;"><b>Published:</b> ${new Date(publishedAt).toLocaleDateString()}</p>
                <div style="margin-top:10px;">
                  <button onclick="downloadVideo('${videoId}')" 
                    style="padding:8px 12px; background:#28a745; color:#fff; border:none; border-radius:5px; cursor:pointer;">
                    Download Video
                  </button>
                  <button onclick="downloadAudio('${videoId}')" 
                    style="padding:8px 12px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer; margin-left:5px;">
                    Download Audio
                  </button>
                </div>
              </div>
            `;
          })
          .join("")}
      </div>
    `;
  }
});

// ==========================
// Download functions
// ==========================

// Show modal for video quality
function downloadVideo(videoId) {
  const modal = document.createElement("div");
  modal.classList.add("modal-overlay");
  modal.innerHTML = `
    <div class="modal-box">
      <h3>Select Video Quality</h3>
      <button onclick="startDownload('${videoId}', '360p')">360p</button>
      <button onclick="startDownload('${videoId}', '720p')">720p</button>
      <button onclick="startDownload('${videoId}', '1080p')">1080p</button>
      <button onclick="closeModal()">Cancel</button>
    </div>
  `;
  document.body.appendChild(modal);
}

// Close modal
function closeModal() {
  const modal = document.querySelector(".modal-overlay");
  if (modal) modal.remove();
}

// Start download (video)
function startDownload(videoId, quality) {
  closeModal();
  window.location.href = `download.php?type=video&id=${videoId}&quality=${quality}`;
}

// Direct audio download
function downloadAudio(videoId) {
  window.location.href = `download.php?type=audio&id=${videoId}`;
}
