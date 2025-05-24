<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8" />  
    <title>Manage Pages</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />  
    <style> 
        html, body {  
            height: 100%;  
            margin: 0;  
            overflow: hidden;  
        }  

        header {  
            position: fixed;  
            top: 0;  
            left: 0;  
            width: 100%;  
            height: 100px;  
            background-color: #f8f9fa;  
            z-index: 1000;  
        }  

        .sidebar {  
            position: fixed;  
            top: 100px;  
            left: 0;  
            width: 250px;  
            height: calc(100vh - 100px);  
            background-color: #f1f1f1;  
            overflow-y: auto;  
            padding: 15px;  
            z-index: 999;  
        }  

        .main-content {  
            position: absolute;  
            top: 100px;  
            left: 250px;  
            width: calc(100% - 250px);  
            height: calc(100vh - 100px);  
            padding: 20px;  
            overflow-y: auto;  
        }   
        fieldset {  
            background: #f8f3f3;  
            padding: 15px;  
            border: 1px solid #ddd !important;  
            margin-top: 20px;  
        }  
        legend {  
            font-size: 0.85rem;  
            padding: 0 10px;  
            width: auto;  
            border: 1px solid #ddd;  
            background: #f8f3f3;  
            color: #5a5a5a;  
            font-weight: 600;  
        }  
        /* Simple toolbar style */  
        .toolbar {  
            border: 1px solid #ccc;  
            background: #eee;  
            padding: 5px 10px;  
            border-radius: 0.25rem 0.25rem 0 0;  
            user-select: none;  
        }  
        .toolbar button {  
            background: none;  
            border: none;  
            padding: 0 8px;  
            font-size: 14px;  
            cursor: pointer;  
        }  
        .toolbar button:hover {  
            background: #ddd;  
        }  
        .editor {  
            border: 1px solid #ccc;  
            min-height: 150px;  
            padding: 8px;  
            border-radius: 0 0 0.25rem 0.25rem;  
            resize: vertical;  
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;  
        }  
        
    </style>  
</head>  
<body>  
<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>


<div class="main-content">  
    <h4>Manage Pages</h4>  

    <fieldset>  
        <legend>FORM FIELDS</legend>  

        <form action="update_page.php" method="post">  
            <div class="mb-3 row align-items-center">  
                <label for="selectPage" class="col-sm-2 col-form-label fw-semibold">select Page</label>  
                <div class="col-sm-6">  
                    <select id="selectPage" name="select_page" class="form-select" required>  
                        <option value="" selected>***Select One***</option>  
                        <option value="home">Home</option>  
                        <option value="about">About Us</option>  
                        <option value="contact">Contact</option>  
                        <!-- Add more pages dynamically if needed -->  
                    </select>  
                </div>  
            </div>  

            <div class="mb-3 row">  
                <label class="col-sm-2 col-form-label fw-semibold">selected Page</label>  
                <div class="col-sm-10">  
                    <!-- Toolbar buttons -->  
                    <div class="toolbar mb-0" role="toolbar" aria-label="Text formatting">  
                        <button type="button" onclick="document.execCommand('bold', false, '');" title="Bold"><b>B</b></button>  
                        <button type="button" onclick="document.execCommand('italic', false, '');" title="Italic"><i>I</i></button>  
                        <button type="button" onclick="document.execCommand('underline', false, '');" title="Underline"><u>U</u></button>  
                        <button type="button" onclick="document.execCommand('strikeThrough', false, '');" title="Strike Through"><s>S</s></button>  
                        <button type="button" onclick="document.execCommand('justifyLeft', false, '');" title="Align Left">Left</button>  
                        <button type="button" onclick="document.execCommand('justifyCenter', false, '');" title="Align Center">Center</button>  
                        <button type="button" onclick="document.execCommand('justifyRight', false, '');" title="Align Right">Right</button>  
                        <button type="button" onclick="document.execCommand('insertUnorderedList', false, '');" title="Bullet List">&bull; List</button>  
                        <button type="button" onclick="document.execCommand('insertOrderedList', false, '');" title="Numbered List">1. List</button>  
                        <button type="button" onclick="document.execCommand('undo', false, '');" title="Undo">Undo</button>  
                        <button type="button" onclick="document.execCommand('redo', false, '');" title="Redo">Redo</button>  
                    </div>  

                    <!-- Editable Page Details -->  
                    <div id="pageDetails" class="editor" contenteditable="true" aria-label="Page Details"></div>  
                    
                    <!-- Hidden textarea to submit content -->  
                    <textarea name="page_details" id="pageDetailsInput" style="display:none;"></textarea>  
                </div>  
            </div>  

            <div class="mb-3 row">  
                <div class="offset-sm-2 col-sm-10">  
                    <button type="submit" class="btn btn-primary">Update</button>  
                </div>  
            </div>  
        </form>  
    </fieldset>  
</div>  

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  

<script>  
    // On form submit, copy contenteditable content to hidden textarea for submission  
    document.querySelector("form").addEventListener("submit", function(event) {  
        const editor = document.getElementById('pageDetails');  
        const textarea = document.getElementById('pageDetailsInput');  
        textarea.value = editor.innerHTML;  
    });  

    // Optional: Load content for selected page dynamically (Here dummy example)  
    document.getElementById('selectPage').addEventListener('change', function() {  
        const val = this.value;  
        const editor = document.getElementById('pageDetails');  

        // Replace with ajax to load content dynamically from server if needed  
        if(val === 'home') {  
            editor.innerHTML = '<h2>Welcome to Home Page</h2><p>This is the home page content.</p>';  
        } else if(val === 'about') {  
            editor.innerHTML = '<h2>About Us</h2><p>Information about us.</p>';  
        } else if(val === 'contact') {  
            editor.innerHTML = '<h2>Contact</h2><p>Contact details go here.</p>';  
        } else {  
            editor.innerHTML = '';  
        }  
    });  
</script>  
</body>  
</html>  