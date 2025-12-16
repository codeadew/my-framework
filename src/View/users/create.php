<?php
$pageContent = <<<HTML
<h1>{$title}</h1>

<form id="createUserForm">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>
    </div>
    
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    
    <button type="submit">Create User</button>
</form>

<div id="result" style="margin-top: 20px;"></div>

<script>
document.getElementById('createUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('result').innerHTML = 
                '<p style="color: green; font-weight: bold;">✅ User created: ' + result.user.name + '</p>';
            this.reset();
        } else {
            let errors = '';
            if (result.errors) {
                for (let field in result.errors) {
                    errors += result.errors[field].join(', ') + '<br>';
                }
            }
            document.getElementById('result').innerHTML = 
                '<p style="color: red; font-weight: bold;">❌ ' + result.message + '</p>' +
                '<p style="color: red;">' + errors + '</p>';
        }
    } catch (error) {
        document.getElementById('result').innerHTML = 
            '<p style="color: red;">Error creating user.</p>';
    }
});
</script>

<p><a href="/users" class="btn">← Back to Users</a></p>
HTML;

$content = $pageContent;
include __DIR__ . '/../layout.php';