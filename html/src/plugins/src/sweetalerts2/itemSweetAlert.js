document.querySelectorAll('.warning.confirm').forEach(function(button) {
    button.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent the <a href="#"> from navigating

        const itemId = this.getAttribute('data-id'); // Get itemID from data attribute

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to PHP handler with ?del=itemID
                window.location.href = `itemView.php?del=${itemId}`;
            }
        });
    });
});
