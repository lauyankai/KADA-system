// Show search and select when clicking the input
document.getElementById('gradeInput').addEventListener('click', function() {
    document.getElementById('gradeSearch').style.display = 'block';
    document.getElementById('gradeSelect').style.display = 'block';
    document.getElementById('gradeSearch').focus();
});

// Hide search and select when clicking outside
document.addEventListener('click', function(e) {
    const container = document.querySelector('.grade-search-container');
    if (!container.contains(e.target)) {
        document.getElementById('gradeSearch').style.display = 'none';
        document.getElementById('gradeSelect').style.display = 'none';
    }
});

// Handle grade selection
document.getElementById('gradeSelect').addEventListener('change', function() {
    const selectedGrade = this.value;
    document.getElementById('gradeInput').value = selectedGrade;
    document.getElementById('gradeSearch').style.display = 'none';
    document.getElementById('gradeSelect').style.display = 'none';
    
    // Remove invalid state if exists
    document.getElementById('gradeInput').classList.remove('is-invalid');
    const errorMessage = document.getElementById('gradeInput').nextElementSibling;
    if (errorMessage && errorMessage.classList.contains('invalid-feedback')) {
        errorMessage.remove();
    }
});

function filterGrades(searchText) {
    const select = document.getElementById('gradeSelect');
    const options = select.getElementsByTagName('option');
    const searchValue = searchText.toLowerCase();
    let visibleCount = 0;

    for (let i = 0; i < options.length; i++) {
        const option = options[i];
        const value = option.value.toLowerCase();
        
        // Skip the first "Pilih" option
        if (value === "") continue;
        
        if (value.includes(searchValue)) {
            option.style.display = '';
            visibleCount++;
        } else {
            option.style.display = 'none';
        }
    }
    
    // Adjust select height based on visible options
    const optionHeight = 40; // height of each option in pixels
    const maxVisibleOptions = 6;
    const visibleHeight = Math.min(visibleCount, maxVisibleOptions) * optionHeight;
    select.style.height = visibleHeight + 'px';
}

// Add styles dynamically
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .grade-search-container {
            position: relative;
        }
        
        #gradeSearch {
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
        }
        
        #gradeSelect option {
            padding: 8px;
        }
        
        #gradeSelect option:checked {
            background-color: #198754;
            color: white;
        }
        
        #gradeSelect {
            max-height: 200px;
        }
    </style>
`); 