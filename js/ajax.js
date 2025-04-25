document.addEventListener("DOMContentLoaded", function() {
    function getCurrentTime() {
        return new Date().toLocaleString(); 
    }

const getForm = document.getElementById("get-form");
if (getForm) {
    getForm.addEventListener("submit", function(event) {
        event.preventDefault(); 

        const formData = new FormData(this); 
        const name = formData.get("name"); 
        const url = `get-page.php?name=${encodeURIComponent(name)}`;

        console.log("Sending GET request with data:", { name }); 

        fetch(url)
            .then(response => response.text())
            .then(data => {
                const currentTime = new Date().toLocaleString(); 
                console.log("GET response received at:", currentTime);

            
                document.getElementById("get-result").innerHTML = `
                    <strong>GET request received</strong><br>
                    Hello, ${name}!<br>
                    <small>Executed at: ${currentTime}</small>
                `;
            })
            .catch(error => {
                console.error("GET request failed:", error);
                alert("Error: " + error.message);
            });
    });
}

    const postForm = document.getElementById("post-form");
    if (postForm) {
        postForm.addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            console.log("Sending POST request with data:", Object.fromEntries(formData));

            fetch("post-page.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                const currentTime = getCurrentTime();
                console.log("POST response received at:", currentTime);

                document.getElementById("post-result").innerHTML = `
                    ${data}<br>
                    <small>Executed at: ${currentTime}</small>
                `;
            })
            .catch(error => {
                console.error("POST request failed:", error);
                alert("Error: " + error.message);
            });
        });
    }
});