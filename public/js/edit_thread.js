thread_edit = document.getElementById("thread_edit");

let thread_text = "";

thread_edit.addEventListener('click', (event) => {
    let thread_id = thread_edit.getAttribute("data-id");
    let edit = document.getElementById("thread-info");
    if (thread_edit.innerHTML !== "Close Edit") {
        let base_url = window.location.pathname
        let request = new XMLHttpRequest();
        request.open('POST', base_url + '/edit', true);

        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                let response = JSON.parse(request.response);
                thread_text = edit.innerHTML;
                edit.innerHTML = response.form;
                let textArea = edit.getElementsByTagName("textarea");
                let form = edit.getElementsByTagName("form");
                textArea.item(0).setAttribute('style', 'height:' + (textArea.item(0).scrollHeight) + 'px;overflow-y:hidden;');
                textArea.item(0).addEventListener('input', () => {
                    textArea.item(0).style.height = 'auto';
                    textArea.item(0).style.height = (textArea.item(0).scrollHeight) + 'px';
                })
                thread_edit.innerHTML = "Close Edit";
                form.item(0).addEventListener('submit', (e) => {
                    e.preventDefault();
                    let request = new XMLHttpRequest();
                    request.open('POST', form.item(0).getAttribute("action"), true);
                    request.setRequestHeader('Content-Type',
                        'application/x-www-form-urlencoded');

                    request.onload = function() {
                        if (request.status >= 200 && request.status < 400) {
                            response = JSON.parse(request.response);
                            thread_edit.innerHTML = "Edit";
                            edit.innerHTML = thread_text;
                            document.getElementById("thread_title").innerHTML = response.title;
                            document.getElementById("thread_description").innerHTML = response.description;
                            document.getElementById("thread_last_update").innerHTML = "Last update: " + response.date;
                        }
                    }

                    request.send($(form.item(0)).serialize());
                })
            }
        }
        request.send();
    } else {
        edit.innerHTML = thread_text;
        thread_edit.innerHTML = "Edit";
    }
})