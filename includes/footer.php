<style>
    .footer {
        background: #000;
        color: #fff;
        padding: 60px 0 30px;
        margin-top: 80px;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        /* Chia đôi: Thông tin và Bản đồ */
        gap: 50px;
        padding: 0 20px;
    }

    .footer-info h3 {
        letter-spacing: 5px;
        text-transform: uppercase;
        font-weight: 300;
        margin-bottom: 25px;
        border-bottom: 1px solid #333;
        padding-bottom: 10px;
        display: inline-block;
    }

    .footer-info p {
        font-size: 13px;
        line-height: 2;
        color: #bbb;
        margin: 10px 0;
    }

    .footer-info a {
        color: #fff;
        text-decoration: none;
        transition: 0.3s;
    }

    .footer-info a:hover {
        color: #888;
    }

    .footer-map iframe {
        filter: grayscale(1) invert(1) opacity(0.7);
        /* Làm bản đồ màu tối cho hợp với style Solpix */
        transition: 0.5s;
        border: 1px solid #333;
    }

    .footer-map iframe:hover {
        filter: grayscale(0) invert(0) opacity(1);
    }

    .footer-bottom {
        text-align: center;
        margin-top: 50px;
        padding-top: 20px;
        border-top: 1px solid #111;
        font-size: 10px;
        letter-spacing: 2px;
        color: #555;
        text-transform: uppercase;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .footer-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-info">
            <h3>Solpix Store</h3>
            <p>
                <strong>Địa chỉ:</strong> Ung Văn Khiêm, P. Đông Xuyên, TP. Long Xuyên, An Giang
            </p>
            <p>
                <strong>Hotline:</strong> <a href="tel:0854171599">0854.171.599</a>
            </p>
            <p>
                <strong>Email:</strong> <a href="mailto:solpixstore67@gmail.com">solpixstore67@gmail.com</a>
            </p>
            <div style="margin-top: 20px;">
                <p>
                    <strong>Kết nối:</strong>
                    <a href="https://www.facebook.com/share/17zHhAKM2g/" target="_blank">Facebook</a> |
                    <a href="https://www.tiktok.com/@solpixstore67" target="_blank">TikTok</a> |
                    <a href="https://m.youtube.com/@solpixstore" target="_blank">YouTube</a>
                </p>
            </div>
        </div>

        <div class="footer-map">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3924.383186178335!2d105.42976397585093!3d10.391090666219468!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x310a0da37546fd7b%3A0x953539cd7673d9e5!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBBbiBHaWFuZyAtIMSQ4bqhaSBo4buNYyBRdeG7ke rYyBHaWEgVFAuSENN!5e0!3m2!1svi!2s!4v1707123456789!5m2!1svi!2s" width="100%"
                height="200"
                style="border:0;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; 2026 Solpix Store. All Rights Reserved.
    </div>
</footer>

</body>

</html>