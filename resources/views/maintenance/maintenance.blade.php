<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maintenance · Kami Segera Kembali</title>
  <!-- Fonts: Inter untuk body modern, Space Mono untuk aksen teknis -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --bg: #f8fafc;
      --surface: #ffffff;
      --card-border: #e2e8f0;
      --accent-primary: #3b82f6;    /* biru profesional */
      --accent-secondary: #8b5cf6;   /* ungu modern */
      --text-primary: #0f172a;
      --text-secondary: #475569;
      --text-muted: #64748b;
      --success: #10b981;
      --warning: #f59e0b;
      --info: #3b82f6;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
      --shadow-glow: 0 0 30px -5px rgba(59, 130, 246, 0.15);
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg);
      color: var(--text-primary);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      position: relative;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
    }

    /* Subtle background pattern — lebih bersih & modern */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image: 
        radial-gradient(circle at 15% 30%, rgba(139, 92, 246, 0.03) 0%, transparent 30%),
        radial-gradient(circle at 85% 70%, rgba(59, 130, 246, 0.03) 0%, transparent 35%);
      pointer-events: none;
      z-index: 0;
    }

    /* Ornamen geometris halus */
    body::after {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--accent-primary), var(--accent-secondary), transparent);
      opacity: 0.5;
      pointer-events: none;
      z-index: 0;
    }

    .maintenance-card {
      position: relative;
      z-index: 2;
      max-width: 640px;
      width: 100%;
      background: var(--surface);
      border-radius: 32px;
      box-shadow: var(--shadow-md), var(--shadow-glow);
      border: 1px solid rgba(255, 255, 255, 0.5);
      backdrop-filter: blur(2px);
      padding: 2.5rem 2rem;
      transition: all 0.2s ease;
      animation: card-appear 0.7s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes card-appear {
      0% { opacity: 0; transform: translateY(20px) scale(0.98); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Header visual — ikon dengan sentuhan glass */
    .icon-section {
      display: flex;
      justify-content: center;
      margin-bottom: 1.8rem;
    }

    .icon-wrapper {
      background: linear-gradient(145deg, #ffffff, #f1f5f9);
      border-radius: 24px;
      padding: 0.75rem;
      box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(59, 130, 246, 0.08);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: transform 0.2s;
    }

    .icon-wrapper:hover {
      transform: scale(1.02);
    }

    .icon-main {
      width: 64px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: white;
      border-radius: 18px;
      box-shadow: inset 0 1px 2px rgba(0,0,0,0.02), 0 2px 4px rgba(0,0,0,0.02);
    }

    .icon-main svg {
      width: 42px;
      height: 42px;
      stroke: var(--accent-primary);
      stroke-width: 1.8;
      transition: stroke 0.2s;
    }

    /* Badge / label status */
    .status-chip {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #f1f5f9;
      color: var(--text-secondary);
      font-size: 0.8rem;
      font-weight: 500;
      letter-spacing: 0.02em;
      padding: 0.3rem 1rem 0.3rem 0.8rem;
      border-radius: 40px;
      margin-bottom: 1.8rem;
      border: 1px solid #e2e8f0;
      backdrop-filter: blur(4px);
      font-family: 'Space Mono', monospace;
      text-transform: uppercase;
    }

    .pulse-dot {
      width: 8px;
      height: 8px;
      border-radius: 10px;
      background: var(--warning);
      box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5);
      animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
      0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
      70% { box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); }
      100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }

    /* Typography */
    h1 {
      font-size: 2.4rem;
      font-weight: 600;
      letter-spacing: -0.03em;
      line-height: 1.2;
      margin-bottom: 1rem;
      color: var(--text-primary);
    }

    .gradient-text {
      background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 80%);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      display: inline-block;
    }

    .description {
      font-size: 1.05rem;
      color: var(--text-secondary);
      margin-bottom: 2.2rem;
      font-weight: 400;
      line-height: 1.6;
    }

    /* Separator elegan */
    .separator {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 2rem;
    }

    .line {
      height: 1px;
      flex: 1;
      background: linear-gradient(90deg, transparent, #cbd5e1, transparent);
    }

    .separator span {
      font-family: 'Space Mono', monospace;
      font-size: 0.7rem;
      color: var(--text-muted);
      letter-spacing: 2px;
      text-transform: uppercase;
    }

    /* Status panel — informasi realtime */
    .service-panel {
      background: #f8fafc;
      border-radius: 20px;
      padding: 1.4rem 1.5rem;
      border: 1px solid #eef2f6;
      margin-bottom: 2.2rem;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.02);
    }

    .service-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.7rem 0;
      border-bottom: 1px solid #e9eef4;
    }

    .service-row:last-child {
      border-bottom: none;
    }

    .service-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .indicator {
      width: 10px;
      height: 10px;
      border-radius: 10px;
      flex-shrink: 0;
    }

    .indicator.online {
      background: var(--success);
      box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.15);
    }

    .indicator.maintenance {
      background: var(--warning);
      box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.15);
      animation: soft-pulse 1.8s infinite;
    }

    .indicator.standby {
      background: var(--info);
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    @keyframes soft-pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.6; }
    }

    .service-name {
      font-weight: 500;
      color: var(--text-primary);
      font-size: 0.95rem;
    }

    .service-status {
      font-size: 0.85rem;
      font-family: 'Space Mono', monospace;
      color: var(--text-muted);
      background: white;
      padding: 0.2rem 0.7rem;
      border-radius: 30px;
      border: 1px solid #dee6ed;
    }

    /* Progress / estimasi — sentuhan user-friendly */
    .eta-message {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background: rgba(59, 130, 246, 0.03);
      padding: 0.9rem 1.5rem;
      border-radius: 60px;
      margin-bottom: 1.8rem;
      border: 1px dashed #b9d0f0;
    }

    .eta-message svg {
      width: 20px;
      height: 20px;
      stroke: var(--accent-primary);
      stroke-width: 1.8;
    }

    .eta-message span {
      font-weight: 500;
      color: var(--text-primary);
    }

    .eta-message .time {
      font-family: 'Space Mono', monospace;
      background: white;
      padding: 0.1rem 0.7rem;
      border-radius: 30px;
      font-size: 0.9rem;
      color: var(--accent-primary);
      border: 1px solid #cbd5e1;
    }

    /* Footer dengan copyright dan link sosial (opsional) */
    .footer-note {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 0.8rem;
      color: var(--text-muted);
      border-top: 1px solid #eef2f6;
      padding-top: 1.2rem;
      margin-top: 0.4rem;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 6px;
      font-weight: 500;
    }

    .brand-dot {
      width: 6px;
      height: 6px;
      border-radius: 6px;
      background: var(--accent-primary);
    }

    .footer-links {
      display: flex;
      gap: 20px;
      font-family: 'Space Mono', monospace;
    }

    .footer-links a {
      color: var(--text-muted);
      text-decoration: none;
      transition: color 0.2s;
      font-size: 0.75rem;
    }

    .footer-links a:hover {
      color: var(--accent-primary);
    }

    /* Responsif */
    @media (max-width: 480px) {
      .maintenance-card {
        padding: 1.8rem 1.2rem;
        border-radius: 28px;
      }
      h1 {
        font-size: 1.9rem;
      }
      .description {
        font-size: 0.95rem;
      }
      .service-panel {
        padding: 1rem 1rem;
      }
      .eta-message {
        flex-wrap: wrap;
        text-align: center;
      }
      .footer-note {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>
  <div class="maintenance-card">
    
    <!-- Ikon modern dengan aksen gradien halus -->
    <div class="icon-section">
      <div class="icon-wrapper">
        <div class="icon-main">
          <!-- Ikon konstruksi / tools — lebih profesional & clean -->
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Chip status maintenance (user-friendly) -->
    <div style="text-align: center;">
      <div class="status-chip">
        <span class="pulse-dot"></span>
        <span>MAINTENANCE MODE · AKTIF</span>
      </div>
    </div>

    <!-- Headline dengan sentuhan gradient -->
    <h1 style="text-align: center;">
      Kami sedang <br><span class="gradient-text">menyempurnakan layanan</span>
    </h1>

    <!-- Deskripsi yang lebih personal & profesional -->
    <p class="description" style="text-align: center;">
      Tim teknis sedang melakukan pembaruan sistem untuk menghadirkan 
      performa yang lebih cepat dan pengalaman yang lebih baik. 
      Semua akan kembali normal dalam waktu singkat.
    </p>

    <!-- Separator -->
    <div class="separator">
      <div class="line"></div>
      <span>status terkini</span>
      <div class="line"></div>
    </div>


    <!-- Footer yang rapi dan profesional -->
    <div class="footer-note">
      <div class="brand">
        <span class="brand-dot"></span>
        <span>LayananDigital</span>
        <span style="margin-left: 6px; opacity: 0.5;">v2.4</span>
      </div>
      <div class="footer-links">
        <a href="#" style="pointer-events: none; opacity: 0.7;">Status</a>
        <a href="#" style="pointer-events: none; opacity: 0.7;">Bantuan</a>
        <span style="color: #94a3b8;">&copy; 2026</span>
      </div>
    </div>

    <!-- Catatan kecil: akan kembali segera (subtle) -->
    <div style="margin-top: 0.8rem; text-align: center; font-size: 0.7rem; color: #94a3b8; font-family: 'Space Mono', monospace; letter-spacing: 1px;">
      TERIMA KASIH ATAS KESABARAN ANDA — KAMI SEGERA KEMBALI
    </div>

    <!-- Kontak Customer Service -->
    @php
        // Ambil data customer service yang aktif
        try {
            $customerServices = \App\Models\CustomerService::where('is_active', true)->get();
        } catch (\Exception $e) {
            $customerServices = collect();
        }
    @endphp

    @if($customerServices->count() > 0)
    <div style="margin-top: 2rem; padding: 1.2rem; background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1); border-radius: 20px; text-align: center;">
      <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">
        Apabila ada hal mendesak, harap hubungi Customer Service kami:
      </p>
      <div style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
        @foreach($customerServices as $cs)
          <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cs->whatsapp_number) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: var(--text-primary); font-weight: 500; font-size: 0.9rem; padding: 0.6rem 1.2rem; background: white; border-radius: 40px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: transform 0.2s, box-shadow 0.2s;">
            <svg style="width: 20px; height: 20px; fill: #25D366;" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.082 22.184c-1.787 0-3.447-.457-4.887-1.246l-5.385 1.413 1.439-5.253c-.84-1.464-1.32-3.149-1.32-4.945 0-5.501 4.499-10 10-10 5.501 0 10 4.499 10 10 0 5.501-4.499 10-10 10z"/></svg>
            {{ $cs->name }} ({{ $cs->whatsapp_number }})
          </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  <!-- Placeholder untuk dynamic data (bisa disesuaikan dengan backend) -->
  <!-- Karena hanya static demo, tapi sudah mendukung konsep modern -->
</body>
</html>