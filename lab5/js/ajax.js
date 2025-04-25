// js/ajax.js
document.addEventListener("DOMContentLoaded", () => {
    const getForm = document.getElementById("get-form");
    const postForm = document.getElementById("post-form");

    if (getForm) {
        getForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const name = document.getElementById("get-name").value;
            const response = await fetch(`/get?name=${encodeURIComponent(name)}`);
            const text = await response.text();
            document.getElementById("get-result").innerHTML = text;
        });
    }

    if (postForm) {
        postForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(postForm);
            const response = await fetch("/post", {
                method: "POST",
                body: new URLSearchParams(formData)
            });
            const text = await response.text();
            document.getElementById("post-result").innerHTML = text;
        });
    }
});