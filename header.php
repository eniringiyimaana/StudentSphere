<?php
require_once 'config.php'; // DB connection if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StudentSphere</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="style.css">

</head>

<body>



<!-- Main Content -->
<div class="content">

  <!-- Top Navbar -->
  <!-- In header.php, replace the search form with this: -->
<form class="d-flex ms-auto" action="search.php" method="get">
    <div class="input-group">
        <input class="form-control" type="search" name="q" 
               placeholder="Search events, news, clubs..." 
               aria-label="Search"
               value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button class="btn btn-primary" type="submit">
            <i class="fas fa-search"></i>
        </button>
    </div>
</form>

 <!-- Spacer between slider and stats -->
  <div class="spacer"></div>







<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="q"]');
    const searchForm = document.querySelector('form[action="search.php"]');
    
    if (searchInput) {
        let suggestionsContainer = document.createElement('div');
        suggestionsContainer.className = 'search-suggestions';
        suggestionsContainer.style.cssText = '
            position: absolute; 
            top: 100%; 
            left: 0; 
            right: 0; 
            background: white; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            display: none; 
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
        ';
        
        searchForm.style.position = 'relative';
        searchForm.appendChild(suggestionsContainer);
        
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (query.length >= 2) {
                fetch(`search_suggestions.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsContainer.innerHTML = '';
                        
                        if (data.length > 0) {
                            data.forEach(item => {
                                const div = document.createElement('div');
                                div.style.cssText = 'padding: 10px; cursor: pointer; border-bottom: 1px solid #eee;';
                                div.innerHTML = `
                                    <i class="fas fa-${item.type} me-2 text-muted"></i>
                                    ${item.title}
                                `;
                                div.addEventListener('click', () => {
                                    searchInput.value = item.title;
                                    suggestionsContainer.style.display = 'none';
                                    searchForm.submit();
                                });
                                suggestionsContainer.appendChild(div);
                            });
                            suggestionsContainer.style.display = 'block';
                        } else {
                            suggestionsContainer.style.display = 'none';
                        }
                    });
            } else {
                suggestionsContainer.style.display = 'none';
            }
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchForm.contains(e.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });
        
        searchInput.addEventListener('blur', function() {
            setTimeout(() => {
                suggestionsContainer.style.display = 'none';
            }, 200);
        });
    }
});
</script>
