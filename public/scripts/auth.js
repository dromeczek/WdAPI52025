document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form.form");
  if (!form) return; // nie jesteśmy na stronie logowania

  const errorBox = document.querySelector("#login-error");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (errorBox) {
      errorBox.textContent = "";
      errorBox.style.display = "none";
    }

    const formData = new FormData(form);

    try {
      const res = await fetch("/login", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "fetch",
          "Accept": "application/json"
        }
      });

      const data = await res.json().catch(() => null);

      if (!res.ok || !data) {
        const msg = (data && data.message) ? data.message : "Wystąpił błąd logowania.";
        if (errorBox) {
          errorBox.textContent = msg;
          errorBox.style.display = "block";
        } else {
          alert(msg);
        }
        return;
      }

      if (data.success) {
        window.location.href = data.redirect || "/dashboard";
      } else {
        const msg = data.message || "Nie udało się zalogować.";
        if (errorBox) {
          errorBox.textContent = msg;
          errorBox.style.display = "block";
        } else {
          alert(msg);
        }
      }
    } catch (err) {
      const msg = "Brak połączenia z serwerem / błąd sieci.";
      if (errorBox) {
        errorBox.textContent = msg;
        errorBox.style.display = "block";
      } else {
        alert(msg);
      }
    }
  });
});
