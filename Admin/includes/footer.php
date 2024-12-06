<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <div class="footer-icons mb-3">
                    <a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span class="copyright">Copyright © 2024. 88 Legacy.</span>
            </div>
        </div>
    </div>
</footer>

<style>
    .sticky-footer {
        width: 100%;
        box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
        background-color: white;
        padding: 10px 0;
        margin-top: auto; /* Ensures it stays at the bottom if content is short */
    }

    .footer-icons {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .footer-icons a {
        color: #4e73df;
        margin: 0 10px;
        font-size: 24px;
        transition: color 0.3s;
    }

    .footer-icons a:hover {
        color: #2e59d9;
    }

    .copyright {
        font-family: 'Nunito', sans-serif;
        font-size: 14px;
        color: #6c757d;
    }

    @media (max-width: 1200px) {
        .footer-icons a {
            margin: 0 8px;
            font-size: 22px;
        }

        .copyright {
            font-size: 13px;
        }
    }

    @media (max-width: 992px) {
        .footer-icons a {
            margin: 0 6px;
            font-size: 20px;
        }

        .copyright {
            font-size: 12px;
        }
    }

    @media (max-width: 768px) {
        .footer-icons a {
            margin: 0 4px;
            font-size: 18px;
        }

        .copyright {
            font-size: 11px;
        }
    }

    @media (max-width: 576px) {
        .footer-icons a {
            margin: 0 2px;
            font-size: 16px;
        }

        .copyright {
            font-size: 10px;
        }
    }
</style>
