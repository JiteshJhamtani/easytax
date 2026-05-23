 
 <style>
 .tooltip-anchor { position: relative; cursor: pointer; }
        
        .gm-tooltip { 
            position: fixed; 
            background: #ffffff; 
            color: #111827; 
            border: 1px solid #e8ecf0;
            border-radius: 12px; 
            padding: 1.25rem; 
            width: 220px; 
            opacity: 0; 
            pointer-events: none; 
            transition: opacity 0.2s ease, transform 0.2s ease;
            transform: translateY(10px); 
            z-index: 9999; 
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.2) !important; 
            text-align: left;
        }
        
        .gm-tooltip.visible { 
            opacity: 1; 
            transform: translateY(0); 
        }
        
        .gm-tooltip::after { 
            content: ''; 
            position: absolute; 
            top: 100%; 
            left: var(--arrow-x, 50%); 
            transform: translateX(-50%); 
            border: 8px solid transparent; 
            border-top-color: #ffffff; 
        }
        
        .gm-tooltip__img { 
            width: 100%; 
            height: 100px; 
            object-fit: cover; 
            border-radius: 8px; 
            margin-bottom: 0.8rem; 
            border: 1px solid #f3f4f6;
        }
        
        .gm-tooltip__name { 
            font-size: 0.95rem; 
            font-weight: 800; 
            margin-bottom: 0.75rem; 
            color: #111827; 
            line-height: 1.3;
        }
        
        .gm-tooltip__row { 
            font-size: 0.8rem; 
            color: #6b7280; 
            display: flex; 
            justify-content: space-between; 
            margin-top: 0.4rem; 
        }
        
        .gm-tooltip__row span:last-child { 
            color: #111827; 
            font-weight: 700; 
        }
        
        .gm-tooltip__unlocked { 
            color: #1E9C5D; 
            font-size: 0.85rem; 
            font-weight: 800; 
            margin-top: 0.8rem; 
        }
        
        .gm-tooltip__locked { 
            color: #ef4444; 
            font-size: 0.85rem; 
            font-weight: 800; 
            margin-top: 0.4rem; 
        }

        /* ── DASHBOARD CARD HOVERS ── */
        .dashboard-card { transition: transform 0.2s, box-shadow 0.2s; }
        .dashboard-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.04) !important; }

        /* ── ROADMAP TIMELINE CSS (App Page Style) ── */
        .sv-card {
            background: #ffffff; border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            border: 1px solid #e8ecf0;
            padding: 1.5rem 2rem;
        }

        .gm-card__top { display: flex; justify-content: space-between; margin-bottom: 3.5rem; }
        .gm-card__label { font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.4rem; }
        .gm-card__title { font-weight: 800; color: #111827; margin: 0; }
        
        .gm-card__count { text-align: right; }
        .gm-card__count-num { font-size: 2.2rem; font-weight: 800; line-height: 1; display: block; color: #111827; }
        .gm-card__count-label { font-size: 0.75rem; font-weight: 600; color: #6b7280; }

        .gm-track-area { position: relative; margin: 1rem 0 4.5rem; }
        .gm-track { height: 8px; border-radius: 99px; background: var(--gm-track); }
        .gm-track__fill { height: 100%; border-radius: 99px; background: var(--gm-accent); transition: width 1.2s ease-out; }

        .gm-dot-anchor { position: absolute; top: 50%; transform: translate(-50%, -50%); display: flex; flex-direction: column; align-items: center; z-index: 10; }
        
        /* The Image Rings */
        .gm-dot-icon { 
            width: 44px; height: 44px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            background: #ffffff; border: 2px solid #e5e7eb; 
            transition: transform 0.2s, border-color 0.2s; position: relative; z-index: 2; 
        }
        .gm-dot-anchor:hover .gm-dot-icon { transform: scale(1.15); }
        
        /* Unlocked Image Rings (Green) */
        .gm-dot-icon.is-unlocked { 
            border-color: var(--gm-accent); 
            box-shadow: 0 0 0 4px var(--gm-track); 
        }

        .gm-dot-label { position: absolute; top: 55px; text-align: center; width: 90px; pointer-events: none; }
        .gm-dot-label__count { font-size: 0.85rem; font-weight: 800; display: block; color: var(--gm-accent); }
        .gm-dot-label__name { font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block;  overflow: hidden; text-overflow: ellipsis; }

        .gm-hint { font-size: 0.85rem; color: #6b7280; display: flex; align-items: center; }
        .gm-hint__pill { font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.8rem; border-radius: 20px; background: var(--gm-track); color: var(--gm-accent); margin-right: 0.5rem; }

        /* ── MULTI-SERVICE CARD CSS (Restored) ── */
        .multi-card { background: #ffffff; border: 1px solid #e8ecf0; border-radius: 16px; padding: 1.5rem 2rem; }
        .tc-period { color: #6b7280; font-size: 0.75rem; letter-spacing: 0.05em; }
        .tc-title { color: #111827; font-size: 1.15rem; }
        .mc-badge { font-size: 0.75rem; font-weight: 700; padding: 0.4rem 0.8rem; border-radius: 20px; text-transform: uppercase;}
        .mc-badge-success { background: #EDF7F4; color: #1E9C5D; }
        .mc-badge-warning { background: #FEF3C7; color: #D97706; }

        /* ── PREMIUM RECENT ACTIVITY TABLE (Restored) ── */
        .modern-table th {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.05em; color: #7a8799; padding-top: 1rem; padding-bottom: 1rem;
        }
        .modern-table td { border-bottom: 1px solid #e8ecf0; padding-top: 1.25rem; padding-bottom: 1.25rem; color: #4a5568; }
        .modern-table tbody tr:hover { background-color: #f8fafc; }
        .modern-table tbody tr:last-child td { border-bottom: none; }

        .premium-badge { font-size: 0.75rem; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 6px; display: inline-block; }
        .premium-badge-success { background: #EDF7F4; color: #1E9C5D; }
        .premium-badge-warning { background: #FEF3C7; color: #D97706; }
        .premium-badge-danger  { background: #FEE2E2; color: #DC2626; }
        .premium-badge-primary { background: #E0E7FF; color: #4338CA; }

        .btn-premium-view {
            font-size: 0.8rem; font-weight: 700; color: #1E9C5D; background: #ffffff;
            border: 1.5px solid #e8ecf0; padding: 0.4rem 0.85rem; border-radius: 8px;
            text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center;
        }
        .btn-premium-view:hover {
            background: #1E9C5D; color: #ffffff; border-color: #1E9C5D;
            text-decoration: none; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30, 156, 93, 0.15);
        }

        /* ── 1. KPI SECTION STYLES ── */
        .kpi-section-wrapper {
            background-color: #EDF7F4;
            border-radius: 16px;
            padding: 1.75rem 2rem 2rem;
            margin-bottom: 2rem;
        }
        .kpi-section-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #157a48;
            margin-bottom: 1.25rem;
        }
        .kpi-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .kpi-icon-green-soft { background: #e6f4ea; color: #1E9C5D; }
        .kpi-icon svg { width: 24px; height: 24px; }
        .kpi-label {
            font-size: 0.75rem; font-weight: 700; color: #7a8799;
            text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.4rem;
        }
        .kpi-value { font-size: 1.8rem; font-weight: 800; line-height: 1; color: #2E3D4E; }  

        </style>