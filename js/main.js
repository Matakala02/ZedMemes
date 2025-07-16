$(document).ready(function () {
  loadMemes();

  $("#loginForm").submit(function (e) {
    e.preventDefault();
    $.ajax({
      url: "php/auth/login.php",
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showToast("success", response.message || "Login successful!");
          setTimeout(() => location.reload(), 1000);
        } else {
          showToast("error", response.message || "Login failed.");
        }
      },
      error: function () {
        showToast("error", "Network or server error. Please try again.");
      },
    });
  });

  $("#registerForm").submit(function (e) {
    e.preventDefault();
    $.ajax({
      url: "php/auth/register.php",
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (res) {
        if (res.success) {
          showToast("success", res.message || "Account created successfully!");
          setTimeout(() => location.reload(), 1000); // Give feedback before reload
        } else {
          showToast("error", res.message || "Registration failed.");
        }
      },
      error: function () {
        showToast("error", "Something went wrong. Please try again.");
      },
    });
  });

  $("#uploadForm").submit(function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
      url: "php/meme/upload.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showToast(
            "success",
            response.message || "Meme uploaded successfully!"
          );
          $("#uploadModal").addClass("hidden");
          $("#uploadForm")[0].reset();
          loadMemes(); // Reload memes
        } else {
          showToast("error", response.message || "Upload failed.");
        }
      },
      error: function () {
        showToast("error", "Something went wrong. Please try again.");
      },
    });
  });
});

function loadMemes() {
  const memeFeed = document.getElementById("memeFeed");
  memeFeed.innerHTML = "";
  const URL = `php/meme/fetch.php`;
  $.ajax({
    url: URL,
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (response && response.success) {
        const { data } = response;
        data.forEach(function (meme) {
          const card = createMemeCard(meme);
          memeFeed.appendChild(card);
        });
      } else {
        const { message } = response;
        memeFeed.innerHTML = document.createElement("div").innerText = message;
        // Maybe a dialog here to.
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", status, error);
      alert("An error occurred while fetching memes. Please try again later.");
    },
  });
}

function createMemeCard(memeData) {
  const { meme, user, reactions } = memeData;

  const memeCard = document.createElement("div");
  memeCard.className = "bg-white rounded-lg shadow-md overflow-hidden mb-6";

  const memeDetails = document.createElement("div");
  memeDetails.className =
    "flex justify-between items-center px-4 py-2 text-sm text-gray-600";

  const memeDetailsUsername = document.createElement("div");
  memeDetailsUsername.className = "font-semibold text-gray-800";
  memeDetailsUsername.innerText = user.username;

  const memeDetailsTime = document.createElement("div");
  memeDetailsTime.className = "text-xs text-gray-500";
  memeDetailsTime.innerText = `${moment(meme.uploaded_at).fromNow()}`;

  memeDetails.appendChild(memeDetailsUsername);
  memeDetails.appendChild(memeDetailsTime);

  const memeBody = document.createElement("div");
  memeBody.className = "w-full";

  const img = document.createElement("img");
  img.className = "w-full h-auto object-contain";
  img.src = meme.meme_url;
  img.alt = meme.meme_url;

  memeBody.appendChild(img);

  const memeFooter = document.createElement("div");
  memeFooter.className =
    "flex justify-between items-center px-4 py-2 bg-gray-50";

  const memeFooterLeft = document.createElement("div");
  memeFooterLeft.className = "flex space-x-4";

  const likeLink = document.createElement("a");
  likeLink.href = meme.meme_id;
  likeLink.className =
    "flex items-center space-x-1 text-blue-600 hover:text-blue-800 cursor-pointer";
  likeLink.dataset.memeId = meme.meme_id;
  likeLink.dataset.reactType = "like";

  const thumbsUp = document.createElement("i");
  thumbsUp.className = "fa fa-thumbs-up";
  const likeCount = document.createElement("span");
  likeCount.innerText = reactions.like;

  likeLink.appendChild(thumbsUp);
  likeLink.appendChild(likeCount);

  likeLink.addEventListener("click", function (e) {
    e.preventDefault();
    handleReaction("like", meme, {
      value: likeCount,
      icon: thumbsUp,
    });
  });

  const upvoteLink = document.createElement("a");
  upvoteLink.href = meme.meme_id;
  upvoteLink.className =
    "flex items-center space-x-1 text-green-600 hover:text-green-800 cursor-pointer";
  upvoteLink.dataset.memeId = meme.meme_id;
  upvoteLink.dataset.reactType = "upvote";

  const upvoteIcon = document.createElement("i");
  upvoteIcon.className = "fa fa-arrow-up";
  const upvoteCount = document.createElement("span");
  upvoteCount.innerText = reactions.upvote;

  upvoteLink.appendChild(upvoteIcon);
  upvoteLink.appendChild(upvoteCount);

  upvoteLink.addEventListener("click", function (e) {
    e.preventDefault();
    handleReaction("upvote", meme, {
      value: upvoteCount,
      icon: upvoteIcon,
    });
  });

  memeFooterLeft.appendChild(likeLink);
  memeFooterLeft.appendChild(upvoteLink);

  const memeFooterRight = document.createElement("div");
  memeFooterRight.className = "flex space-x-4";

  const shareLink = document.createElement("a");
  shareLink.href = meme.meme_id;
  shareLink.className = "text-gray-600 hover:text-gray-800 cursor-pointer";
  shareLink.dataset.memeId = meme.meme_id;
  shareLink.dataset.reactType = "share";
  shareLink.addEventListener("click", function (e) {
    e.preventDefault();
    handleReaction("share", meme);
  });

  const shareIcon = document.createElement("i");
  shareIcon.className = "fa fa-share";
  shareLink.appendChild(shareIcon);

  const downloadLink = document.createElement("a");
  // downloadLink.href = meme.meme_url;
  // downloadLink.download = meme.meme_url;
  prepareDownload(meme, downloadLink);
  downloadLink.className = "text-gray-600 hover:text-gray-800 cursor-pointer";
  downloadLink.dataset.memeId = meme.meme_id;
  downloadLink.dataset.reactType = "download";

  downloadLink.addEventListener("click", function (e) {
    handleReaction("download", meme);
  });

  const downloadIcon = document.createElement("i");
  downloadIcon.className = "fa fa-download";
  downloadLink.appendChild(downloadIcon);

  memeFooterRight.appendChild(shareLink);
  memeFooterRight.appendChild(downloadLink);

  memeFooter.appendChild(memeFooterLeft);
  memeFooter.appendChild(memeFooterRight);

  memeCard.appendChild(memeBody);
  memeCard.appendChild(memeDetails);
  memeCard.appendChild(memeFooter);

  return memeCard;
}

function handleReaction(reaction_type, meme, elements = {}) {
  const { meme_id, meme_url } = meme;
  const { value, icon } = elements;

  $.ajax({
    url: "php/auth/is_logged_in.php",
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (response && response.success) {
        if (reaction_type === "like" || reaction_type === "upvote") {
          $.ajax({
            url: "php/meme/react.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({
              meme_id: meme_id,
              reaction_type: reaction_type,
            }),
            dataType: "json",
            success: function (res) {
              if (res && res.success) {
                const { count } = res;

                if (value) value.innerText = count;

                if (icon) {
                  icon.classList.remove("text-gray-500");
                  if (reaction_type === "like") {
                    icon.classList.add("text-blue-600");
                  } else if (reaction_type === "upvote") {
                    icon.classList.add("text-green-600");
                  }
                }

                showToast("success", `You ${reaction_type}d this meme.`);
              } else {
                showToast("error", res.message || "Reaction failed.");
              }
            },
            error: function () {
              showToast("error", "Could not send your reaction.");
            },
          });
        } else if (reaction_type === "download") {
          showToast("info", "Download started.");
        } else if (reaction_type === "share") {
          handleShare(meme_url);
          showToast("info", "Share link copied!");
        }
      } else {
        showToast("warning", "Please log in to react.");
      }
    },
    error: function () {
      showToast("error", "Failed to verify login status.");
    },
  });
}

async function handleShare(memeUrl) {
  if (navigator.share) {
    try {
      await navigator.share({
        title: "Meme from zedmemes",
        text: "Hey... take a look at this.",
        url: memeUrl,
      });
    } catch (err) {
      // Show a dialog message
    }
  } else {
    alert("Sharing not supported on this browser. Copy the link: " + memeUrl);
  }
}

function showToast(type, message) {
  const toast = document.createElement("div");

  const typeStyles = {
    success: "bg-green-500 border-green-600",
    error: "bg-red-500 border-red-600",
    warning: "bg-yellow-500 border-yellow-600 text-black",
    info: "bg-blue-500 border-blue-600",
  };

  const style = typeStyles[type] || typeStyles.info;

  toast.className = `
    toast-notification
    fixed bottom-4 left-4
    max-w-xs w-full
    flex items-center justify-between
    px-4 py-3 rounded shadow-lg
    text-white border-l-4
    ${style}
    animate-slide-in
  `;

  toast.innerHTML = `
    <span class="flex-1 mr-3">${message}</span>
    <button class="text-white font-bold text-xl leading-none hover:text-gray-300">&times;</button>
  `;

  toast.querySelector("button").addEventListener("click", () => {
    toast.remove();
  });

  setTimeout(() => {
    toast.remove();
  }, 5000);
  document.body.appendChild(toast);
}

function prepareDownload(meme, downloadLink) {
  $.ajax({
    url: "php/auth/is_logged_in.php",
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (response && response.success) {
        downloadLink.href = meme.meme_url;
        downloadLink.download = meme.meme_url;
      }
    },
  });
}

function togglePassword(inputId, toggleBtn) {
  const input = document.getElementById(inputId);
  const icon = toggleBtn.querySelector("i");
  const isPassword = input.type === "password";
  input.type = isPassword ? "text" : "password";

  icon.classList.toggle("fa-eye", !isPassword);
  icon.classList.toggle("fa-eye-slash", isPassword);
}

function showFileName(input) {
  const fileDisplay = document.getElementById("fileNameDisplay");
  if (input.files && input.files.length > 0) {
    fileDisplay.textContent = input.files[0].name;
  } else {
    fileDisplay.textContent = "";
  }
}
