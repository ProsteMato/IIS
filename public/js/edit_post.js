edit_buttons = document.querySelectorAll(".edit-post");

document.querySelectorAll("textarea").forEach((text_area) => {
    text_area.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    text_area.addEventListener('input', () => {
        text_area.style.height = 'auto';
        text_area.style.height = (text_area.scrollHeight) + 'px';
    })
})

let text = "";

edit_buttons.forEach((edit_button) => {
    edit_button.addEventListener('click', (event) => {
        let post_id = edit_button.getAttribute("data-id")
        let edit = document.getElementById("edit"+post_id);
        if (edit_button.innerHTML !== "Close Edit") {
            let base_url = window.location.pathname
            let request = new XMLHttpRequest();
            request.open('POST', base_url + '/post/' + post_id + '/edit', true);

            request.onload = function() {
                if (this.status >= 200 && this.status < 400) {
                    let response = JSON.parse(request.response);
                    text = edit.innerHTML;
                    edit.innerHTML = response.form;
                    let textArea = edit.getElementsByTagName("textarea");
                    let form = edit.getElementsByTagName("form");
                    textArea.item(0).setAttribute('style', 'height:' + (textArea.item(0).scrollHeight) + 'px;overflow-y:hidden;');
                    textArea.item(0).addEventListener('input', () => {
                        textArea.item(0).style.height = 'auto';
                        textArea.item(0).style.height = (textArea.item(0).scrollHeight) + 'px';
                    })
                    edit_button.innerHTML = "Close Edit";
                    form.item(0).addEventListener('submit', (e) => {
                        e.preventDefault();
                        let request = new XMLHttpRequest();
                        request.open('POST', form.item(0).getAttribute("action"), true);
                        request.setRequestHeader('Content-Type',
                        'application/x-www-form-urlencoded');

                        request.onload = function() {
                            if (request.status >= 200 && request.status < 400) {
                                response = JSON.parse(request.response);
                                edit.innerHTML = "<p style='word-break: break-word'>" + response.message + "</p>";
                                let last_update = document.getElementById("last_update" + post_id);
                                last_update.innerHTML = "Last update: " + response.date;
                                edit_button.innerHTML = "Edit";
                            }
                        }

                        request.send($(form.item(0)).serialize());
                    })
                }
            }
            request.send();
        } else {
            edit.innerHTML = text;
            edit_button.innerHTML = "Edit";
        }
    })
})