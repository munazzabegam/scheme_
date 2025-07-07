<footer class="footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Scheme Manager</h3>
                </div>
                <p class="footer-description">
                    Comprehensive scheme management system for efficient administration and customer service.
                </p>
                <div class="footer-social">
                    <a href="#" class="social-link">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="../dashboard/">Dashboard</a></li>
                    <li><a href="../customers/">Customers</a></li>
                    <li><a href="../schemes/">Schemes</a></li>
                    <li><a href="../payments/">Payments</a></li>
                    <li><a href="../reports/">Reports</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <ul class="footer-links">
                    <li><a href="../help/">Help Center</a></li>
                    <li><a href="../contact/">Contact Us</a></li>
                    <li><a href="../faq/">FAQ</a></li>
                    <li><a href="../tutorials/">Tutorials</a></li>
                    <li><a href="../feedback/">Feedback</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>System Info</h4>
                <div class="system-info">
                    <div class="info-item">
                        <span class="info-label">Version:</span>
                        <span class="info-value">v2.1.0</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Updated:</span>
                        <span class="info-value"><?php echo date('M d, Y'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Server Time:</span>
                        <span class="info-value" id="server-time"><?php echo date('H:i:s'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p>&copy; <?php echo date('Y'); ?> Scheme Manager. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="../privacy/">Privacy Policy</a>
                    <a href="../terms/">Terms of Service</a>
                    <a href="../cookies/">Cookie Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: #e2e8f0;
        margin-top: 4rem;
        position: relative;
    }

    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, #64748b, transparent);
    }

    .footer-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .footer-content {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 3rem;
        padding: 3rem 0 2rem 0;
    }

    .footer-section h4 {
        color: white;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .footer-section h4::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 30px;
        height: 2px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 1px;
    }

    .footer-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1rem;
    }

    .footer-logo {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    .footer-brand h3 {
        color: white;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .footer-description {
        color: #94a3b8;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    .footer-social {
        display: flex;
        gap: 12px;
    }

    .social-link {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e2e8f0;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        transform: translateY(-2px);
    }

    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 0.8rem;
    }

    .footer-links a {
        color: #94a3b8;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .footer-links a::before {
        content: '→';
        color: #667eea;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .footer-links a:hover {
        color: white;
        transform: translateX(5px);
    }

    .footer-links a:hover::before {
        transform: translateX(3px);
    }

    .system-info {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 6px;
        border-left: 3px solid #667eea;
    }

    .info-label {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    .info-value {
        color: white;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .footer-bottom {
        border-top: 1px solid #475569;
        padding: 1.5rem 0;
    }

    .footer-bottom-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .footer-bottom p {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    .footer-bottom-links {
        display: flex;
        gap: 2rem;
    }

    .footer-bottom-links a {
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .footer-bottom-links a:hover {
        color: #667eea;
    }

    @media (max-width: 1024px) {
        .footer-content {
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
    }

    @media (max-width: 768px) {
        .footer-container {
            padding: 0 1rem;
        }
        
        .footer-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 2rem 0 1rem 0;
        }
        
        .footer-bottom-content {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        
        .footer-bottom-links {
            gap: 1rem;
        }
    }
</style>

<script>
    // Update server time every second
    function updateServerTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('server-time').textContent = timeString;
    }
    
    setInterval(updateServerTime, 1000);
</script> 