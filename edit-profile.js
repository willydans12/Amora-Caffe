
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: "#593b2b",
              secondary: "#10B981",
            },
            borderRadius: {
              none: "0px",
              sm: "4px",
              DEFAULT: "8px",
              md: "12px",
              lg: "16px",
              xl: "20px",
              "2xl": "24px",
              "3xl": "32px",
              full: "9999px",
              button: "8px",
            },
          },
        },
      };
      // Toggle password visibility
      document.querySelectorAll(".ri-eye-line").forEach((icon) => {
        icon.addEventListener("click", function () {
          const input = this.previousElementSibling;
          if (input.type === "password") {
            input.type = "text";
            this.classList.replace("ri-eye-line", "ri-eye-off-line");
          } else {
            input.type = "password";
            this.classList.replace("ri-eye-off-line", "ri-eye-line");
          }
        });
      });

      // Form validation would go here in a real implementation
    
