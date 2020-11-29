const like_btns = document.querySelectorAll(".like-btn");
const dislike_btns = document.querySelectorAll(".dislike-btn");


function like_dislike(selector) {
    selector.forEach(
        function(like_btn) {
            like_btn.addEventListener("click", function (e){
                e.preventDefault();
                like_btn.setAttribute('disabled', true);

                let id = e.target.getAttribute("data-id");
                let rating = document.getElementById("rating" + id);
                let base_url = window.location.pathname;
                let action = "";
                let request = new XMLHttpRequest();
                if (e.target.id === "thread_like"+id || e.target.id === "thread_dislike"+id) {
                    request.open("POST", base_url + "/liker", true);
                } else {
                    request.open("POST", base_url + "/post/" + id + "/liker", true);
                }
                request.setRequestHeader("Content-Type", "application/json");
                if (e.target.classList.contains("fa-thumbs-o-up")) {
                    action = "like";
                } else if (e.target.classList.contains("fa-thumbs-up")) {
                    action = "unlike";
                } else if (e.target.classList.contains("fa-thumbs-o-down")) {
                    action = "dislike";
                } else if (e.target.classList.contains("fa-thumbs-down")) {
                    action = "undislike";
                }

                request.onload = function() {
                    if (this.status >= 200 && this.status < 400) {
                        let resp = JSON.parse(this.response);
                        if (e.target.id === "thread_like"+id || e.target.id === "thread_dislike"+id) {
                            if (action === "like" || action === "unlike") {
                                e.target.previousElementSibling.
                                    previousElementSibling.previousElementSibling.
                                    innerText = "Rating: " + resp[0].rating;
                            } else if (action === "dislike" || action === "undislike") {
                                e.target.previousElementSibling.
                                    previousElementSibling.
                                    innerText = "Rating: " + resp[0].rating;
                            }
                        } else {
                            rating.innerHTML = "Post rating: " + resp[0].rating;
                        }


                        if (e.target.classList.contains("fa-thumbs-o-up")) {
                            e.target.classList.add("fa-thumbs-up");
                            e.target.classList.remove("fa-thumbs-o-up");
                        } else if (e.target.classList.contains("fa-thumbs-up")) {
                            e.target.classList.remove("fa-thumbs-up");
                            e.target.classList.add("fa-thumbs-o-up");
                        } else if (e.target.classList.contains("fa-thumbs-o-down")) {
                            e.target.classList.add("fa-thumbs-down");
                            e.target.classList.remove("fa-thumbs-o-down");
                        } else if (e.target.classList.contains("fa-thumbs-down")) {
                            e.target.classList.remove("fa-thumbs-down");
                            e.target.classList.add("fa-thumbs-o-down");
                        }
                        if (e.target.id === "thread_like"+id || e.target.id === "thread_dislike"+id) {
                            if(action === "like") {
                                e.target.previousElementSibling.
                                classList.remove("fa-thumbs-down");
                                e.target.previousElementSibling.
                                classList.add("fa-thumbs-o-down");
                            } else if (action === "dislike") {
                                e.target.nextElementSibling
                                    .classList.remove("fa-thumbs-up");
                                e.target.nextElementSibling
                                    .classList.add("fa-thumbs-o-up");
                            }
                        } else {
                            if(action === "like") {
                                e.target.nextElementSibling.
                                classList.remove("fa-thumbs-down");
                                e.target.nextElementSibling.
                                classList.add("fa-thumbs-o-down");
                            } else if (action === "dislike") {
                                e.target.previousElementSibling
                                    .classList.remove("fa-thumbs-up");
                                e.target.previousElementSibling
                                    .classList.add("fa-thumbs-o-up");
                            }
                        }
                    }
                    like_btn.removeAttribute('disabled');
                };

                request.send(JSON.stringify({
                    "action": action
                }));
            });
        }
    );
}

like_dislike(like_btns);
like_dislike(dislike_btns);
