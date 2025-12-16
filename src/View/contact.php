<?php
$pageContent = <<<HTML
<h1>{$title}</h1>
<p>Get in touch with us using the form below.</p>

<form id="contactForm">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>
    </div>
    
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    
    <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" rows="5" required></textarea>
    </div>
    
    <button type="submit">Send Message</button>
</form>

<div id="result" style="margin-top: 20px;"></div>

<script>
document.getElementById('contactForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/contact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('result').innerHTML = 
                '<p style="color: green; font-weight: bold;">✅ ' + result.message + '</p>';
            this.reset();
        } else {
            document.getElementById('result').innerHTML = 
                '<p style="color: red; font-weight: bold;">❌ ' + result.message + '</p>';
        }
    } catch (error) {
        document.getElementById('result').innerHTML = 
            '<p style="color: red;">Error sending message.</p>';
    }
});
</script>

<p><a href="/" class="btn">← Back to Home</a></p>
HTML;

$content = $pageContent;
include __DIR__ . '/layout.php';