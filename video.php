<div class="video-container">
    <div class="video-wrapper">
        <video controls playsinline width="100%">
            <source src="stream_oauth2.php" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ thẻ video.
        </video>
    </div>
</div>
<style>
    .video-wrapper {
        max-width: 900px; /* Độ rộng tối đa bạn muốn */
        margin: 0 auto;   /* Căn giữa trình phát */
    }

    video {
        width: 100%;      /* Luôn chiếm hết khung của video-wrapper */
        height: auto;
        border-radius: 12px; /* Bo góc cho hiện đại */
        box-shadow: 0 10px 30px rgba(0,0,0,0.5); /* Tạo hiệu ứng đổ bóng */
        outline: none;
    }
</style>