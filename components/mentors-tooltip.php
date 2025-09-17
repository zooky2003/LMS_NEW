<?php
// Mentors tooltip component in PHP (Tailwind + vanilla JS)
// Usage: include this file on the landing page.

$mentors = $mentors ?? [
  [
    'id' => 1,
    'name' => 'Hemal Pramuditha',
    'designation' => 'Software Engineer',
    'image' => 'mentors/hemal.png',
  ],
  [
    'id' => 2,
    'name' => 'Dhananjaya',
    'designation' => 'Product Manager',
    'image' => 'mentors/kota.png',
  ],
  [
    'id' => 3,
    'name' => 'Isira Imantha',
    'designation' => 'Data Scientist',
    'image' => 'mentors/isira.png',
  ],
  [
    'id' => 4,
    'name' => 'Ishan Ranaweera',
    'designation' => 'UX Designer',
    'image' => 'mentors/ishan.png',
  ],
  [
    'id' => 5,
    'name' => 'Janani Chathurtha',
    'designation' => 'Soap Developer',
    'image' => 'mentors/janani.png',
  ],
  [
    'id' => 6,
    'name' => 'Heshan Gunasekara',
    'designation' => 'The Explorer',
    'image' => 'mentors/heshan.png',
  ],
  [
    'id' => 7,
    'name' => 'Kaweesha Rathnayake',
    'designation' => 'AI Researcher',
    'image' => 'mentors/rathnayake.png',
  ],
  [
    'id' => 8,
    'name' => 'Tharushi Lakshani',
    'designation' => 'Cloud Architect',
    'image' => 'mentors/tharushi.jpg',
  ],
  [
    'id' => 9,
    'name' => 'Shohani Savindya',
    'designation' => 'DevOps Engineer',
    'image' => 'mentors/CV/shanoshi/shohani.jpg',
  ],
  [
    'id' => 10,
    'name' => 'Minaya Amarasekara',
    'designation' => 'Cybersecurity Lead',
    'image' => 'mentors/minaya.jpg',
  ],
];
?>

<section class="py-16 relative" id="mentors">
  <div class="mx-auto w-full max-w-6xl px-4">
    <div class="rounded-3xl border border-white/20 bg-white/10 backdrop-blur-xl shadow-[0_8px_24px_rgba(2,6,23,0.15)] ring-1 ring-white/10 px-6 py-10">
      <div class="mb-8 text-center">
        <h2 class="text-3xl font-bold tracking-tight text-white">Meet Our Mentors</h2>
        <p class="mt-2 text-white/80">Learn from world‑class professionals dedicated to your success</p>
      </div>

      <div id="mentorsTooltip" class="flex flex-row flex-wrap items-center justify-center gap-4">
      <?php foreach ($mentors as $m): ?>
        <div class="group relative -mr-2" data-mentor-id="<?php echo (int)$m['id']; ?>">
          <!-- Tooltip -->
          <div
            class="pointer-events-none invisible absolute -top-16 left-1/2 z-50 -translate-x-1/2 scale-95 opacity-0 transition-all duration-200 group-hover:visible group-hover:scale-100 group-hover:opacity-100"
            data-tooltip
            style="will-change: transform;"
          >
            <div
              class="relative flex flex-col items-center justify-center whitespace-nowrap rounded-md bg-black px-4 py-2 text-xs shadow-xl"
            >
              <div class="absolute inset-x-10 -bottom-px z-30 h-px w-[20%] bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
              <div class="absolute -bottom-px left-10 z-30 h-px w-[40%] bg-gradient-to-r from-transparent via-sky-500 to-transparent"></div>
              <div class="relative z-30 text-base font-bold text-white"><?php echo htmlspecialchars($m['name']); ?></div>
              <div class="text-xs text-white/90"><?php echo htmlspecialchars($m['designation']); ?></div>
            </div>
          </div>

          <!-- Avatar -->
          <img
            src="<?php echo htmlspecialchars($m['image']); ?>"
            alt="<?php echo htmlspecialchars($m['name']); ?>"
            width="100" height="100"
            class="relative !m-0 h-14 w-14 rounded-full border-2 border-white object-cover object-top !p-0 transition duration-300 group-hover:z-30 group-hover:scale-105 shadow-sm"
            data-avatar
          />
        </div>
      <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<script>
// Vanilla JS hover + tooltip motion inspired by framer-motion version
(function () {
  const root = document.getElementById('mentorsTooltip');
  if (!root) return;

  const SPRING_STIFFNESS = 0.12; // lower = softer
  const SPRING_DAMPING = 0.18;   // higher = more damping

  const items = Array.from(root.querySelectorAll('[data-avatar]'));
  const state = new WeakMap();

  function onMouseMove(e) {
    const img = e.currentTarget;
    const wrapper = img.closest('[data-mentor-id]');
    const tooltip = wrapper.querySelector('[data-tooltip]');
    if (!tooltip) return;

    const rect = img.getBoundingClientRect();
    const offsetX = e.clientX - rect.left; // 0..w
    const half = rect.width / 2;
    const x = Math.max(-100, Math.min(100, (offsetX - half) / half * 100));

    let s = state.get(img);
    if (!s) {
      s = { x: 0, v: 0, raf: 0 };
      state.set(img, s);
    }
    s.target = x;

    if (!s.raf) tick(img, tooltip);
  }

  function onMouseEnter(e) {
    const img = e.currentTarget;
    let s = state.get(img);
    if (!s) { s = { x: 0, v: 0, raf: 0, target: 0 }; state.set(img, s); }
    s.hover = true;
  }

  function onMouseLeave(e) {
    const img = e.currentTarget;
    const wrapper = img.closest('[data-mentor-id]');
    const tooltip = wrapper.querySelector('[data-tooltip]');
    const s = state.get(img);
    if (!s) return;
    s.hover = false;
    s.target = 0; // ease back to center
    if (!s.raf) tick(img, tooltip);
  }

  function tick(img, tooltip) {
    const s = state.get(img);
    if (!s) return;
    cancelAnimationFrame(s.raf);
    s.raf = requestAnimationFrame(() => tick(img, tooltip));

    // critically damped spring-ish integration
    const toTarget = (s.target || 0) - (s.x || 0);
    s.v = (s.v || 0) * (1 - SPRING_DAMPING) + toTarget * SPRING_STIFFNESS;
    s.x = (s.x || 0) + s.v;

    const rotate = (s.x / 100) * 45;      // -45..45deg
    const translateX = (s.x / 100) * 50;  // -50..50px
    tooltip.style.transform = `translateX(${translateX}px) rotate(${rotate}deg)`;

    // stop when close to rest
    if (Math.abs(toTarget) < 0.1 && Math.abs(s.v) < 0.1) {
      tooltip.style.transform = 'translateX(0px) rotate(0deg)';
      cancelAnimationFrame(s.raf);
      s.raf = 0;
      return;
    }
  }

  items.forEach((img) => {
    img.addEventListener('mousemove', onMouseMove);
    img.addEventListener('mouseenter', onMouseEnter);
    img.addEventListener('mouseleave', onMouseLeave);
  });
})();
</script>
