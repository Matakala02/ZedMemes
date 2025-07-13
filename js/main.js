$(document).ready(function () {
  // Load memes on page load
  loadMemes();

  // LOGIN
  $("#loginForm").submit(function (e) {
    e.preventDefault();
    $.post(
      "php/auth/login.php",
      $(this).serialize(),
      function (res) {
        if (res.success) {
          location.reload();
        } else {
          alert(res.message);
        }
      },
      "json"
    );
  });

  // REGISTER
  $("#registerForm").submit(function (e) {
    e.preventDefault();
    $.post(
      "php/auth/register.php",
      $(this).serialize(),
      function (res) {
        if (res.success) {
          location.reload();
        } else {
          alert(res.message);
        }
      },
      "json"
    );
  });

  // UPLOAD
  $("#uploadForm").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
      url: "php/memes/upload.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (res) {
        if (res.success) {
          $("#uploadModal").addClass("hidden");
          $("#uploadForm")[0].reset();
          loadMemes();
        } else {
          alert(res.message);
        }
      },
    });
  });

  // LIKE / UPVOTE handlers
  $("#memeFeed").on("click", ".react-btn", function () {
    const memeId = $(this).data("meme");
    const type = $(this).data("type");

    $.post(
      "php/memes/react.php",
      { meme_id: memeId, type },
      function (res) {
        if (res.success) {
          $(`#${type}-count-${memeId}`).text(res.count);
        } else {
          alert(res.message);
        }
      },
      "json"
    );
  });

  // SHARE Meme (copies image URL)
  $("#memeFeed").on("click", ".share-btn", function () {
    const link = $(this).data("link");
    navigator.clipboard
      .writeText(link)
      .then(() => {
        alert("Link copied!");
      })
      .catch(() => {
        alert("Failed to copy link.");
      });
  });

  // DOWNLOAD Meme (triggers browser save)
  $("#memeFeed").on("click", ".download-btn", function () {
    const url = $(this).data("url");
    const a = document.createElement("a");
    a.href = url;
    a.download = "";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  });
});

function loadMemes() {
  $.get("php/memes/fetch.php", function (data) {
    $("#memeFeed").html(data);
  });
}
