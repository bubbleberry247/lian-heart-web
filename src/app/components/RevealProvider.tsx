"use client";

import { useEffect } from "react";

export function RevealProvider({ children }: { children: React.ReactNode }) {
  useEffect(() => {
    const targets = document.querySelectorAll("[data-reveal]");
    if (!targets.length) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;

          const element = entry.target as HTMLElement;
          const delay = element.getAttribute("data-delay");

          if (delay) {
            element.style.setProperty("--reveal-delay", delay + "ms");
          }

          element.classList.add("is-inview");

          if (element.hasAttribute("data-wipe")) {
            element.classList.add("is-animating");
            window.setTimeout(() => element.classList.remove("is-animating"), 1200);
          }

          observer.unobserve(element);
        });
      },
      {
        threshold: 0.18,
        rootMargin: "0px 0px -12% 0px",
      },
    );

    targets.forEach((element) => observer.observe(element));

    return () => observer.disconnect();
  }, []);

  return <>{children}</>;
}
