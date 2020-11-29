const like_btns = document.querySelectorAll(".like-btn");
const dislike_btns = document.querySelectorAll(".dislike-btn");

let last_action = "";

function like_dislike(selector) {
    selector.forEach(
        function(like_btn) {
            like_btn.addEventListener("click", function (e){
                e.stopImmediatePropagation();
                let id = e.target.getAttribute("data-id");
                let group_id = window.location.pathname;
                let action = "";
                let request = new XMLHttpRequest();

                if (e.target.classList.contains("fa-thumbs-o-up")) {
                    action = "like";
                } else if (e.target.classList.contains("fa-thumbs-up")) {
                    action = "unlike";
                } else if (e.target.classList.contains("fa-thumbs-o-down")) {
                    action = "dislike";
                } else if (e.target.classList.contains("fa-thumbs-down")) {
                    action = "undislike";
                }

                if (last_action === "like" && action === "like") {
                    action = "unlike"
                } else if (last_action === "unlike" && action === "unlike") {
                    action = "like"
                } else if (last_action === "dislike" && action === "dislike") {
                    action = "undislike"
                } else if (last_action === "undislike" && action === "undislike") {
                    action = "dislike"
                }

                request.open("POST", group_id + "/thread/show/"+ id +"/liker", true);
                request.setRequestHeader("Content-Type", "application/json");

                request.onload = function() {
                    if (this.status >= 200 && this.status < 400) {
                        let resp = JSON.parse(this.response);
                        if (action === "like" || action === "unlike") {
                            e.target.previousElementSibling.
                                previousElementSibling.previousElementSibling.
                                innerText = "Rating: " + resp[0].rating;
                        } else if (action === "dislike" || action === "undislike") {
                            e.target.previousElementSibling.
                                previousElementSibling.
                                innerText = "Rating: " + resp[0].rating;
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

                        last_action = action;
                    }
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
