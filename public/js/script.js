
    // Global variables
    let selectedValue = '';
    
    // Function definitions
    // function toggleDropdown() {
    //     const dropdownList = document.getElementById('dropdown-list');
    //     const dropdown = document.getElementById('customDropdown');
        
    //     if (dropdownList.style.display === 'block') {
    //         dropdownList.style.display = 'none';
    //         dropdown.classList.remove('open');
    //     } else {
    //         dropdownList.style.display = 'block';
    //         dropdown.classList.add('open');
    //     }
    // }
    
    // function selectClass(value) {
    //     selectedValue = value;
    //     const selectedText = event.target.textContent || '-- Pilih Kelas --';
    //     document.getElementById('selected-class').textContent = selectedText;
    //     document.getElementById('kelas_id').value = value;
    //     document.getElementById('dropdown-list').style.display = 'none';
    //     document.getElementById('customDropdown').classList.remove('open');
    // }
    
    function handleSelect() {
        const selectedValue = document.getElementById('kelas_id').value;
        if (selectedValue) {
            window.location.href = '{{ url("/jadwal/kelas") }}/' + selectedValue;
        } else {
            alert('Silakan pilih kelas terlebih dahulu');
        }
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('.custom-dropdown');
        if (!dropdown.contains(event.target)) {
            document.getElementById('dropdown-list').style.display = 'none';
            document.getElementById('customDropdown').classList.remove('open');
        }
    });

    