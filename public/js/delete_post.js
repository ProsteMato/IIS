delete_buttons = document.querySelectorAll(".delete-post");


delete_buttons.forEach((delete_button) => {
    delete_button.addEventListener('click', (event) => {
        let post_id = delete_button.getAttribute("data-id")
        let base_url = window.location.pathname
        let request = new XMLHttpRequest();
        request.open('POST', base_url + '/post/' + post_id + '/delete', true);

        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                document.getElementById("post"+post_id).remove();
            }
        }
        request.send();
    })
})