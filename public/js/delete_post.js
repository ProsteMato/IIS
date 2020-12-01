delete_buttons = document.querySelectorAll(".delete-post");


delete_buttons.forEach((delete_button) => {
    delete_button.addEventListener('click', (event) => {
        let post_id = delete_button.getAttribute("data-id")
        let base_url = window.location.pathname
        let request = new XMLHttpRequest();
        request.open('POST', base_url + '/post/' + post_id + '/delete', true);

        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                let response = JSON.parse(this.response);
                document.getElementById("post"+post_id).remove();
                let thread_last_update = document.getElementById("thread_last_update");
                thread_last_update.innerText = "Last update: " + response.date;
                document.getElementById("thread_post_count").innerHTML = response.postCount + " posts";
            }
        }
        request.send();
    })
})