<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Realtime Conveyor Queue - Pro Display (Equal Height)</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700;900&display=swap" rel="stylesheet">
<style>
    /* Reset และ Base */
    * { box-sizing: border-box; margin:0; padding:0; }
    body {
        font-family: 'Prompt', sans-serif;
        background: linear-gradient(135deg, #0d1b2a, #1a253d);
        display: flex;
        justify-content: center; /* จัดให้อยู่ตรงกลางของ Body */
        align-items: center;
        min-height: 100vh;
        color: #fff;
        overflow-x: hidden;
        padding: 2vh 0;
    }

    /* 📌 CONTAINER หลักสำหรับจัด Layout ให้สูงเท่ากัน */
    .main-wrapper {
        display: flex; 
        flex-direction: row; 
        gap: 20px; 
        width: 95%; 
        max-width: 1600px;
        align-items: stretch; /* 🔥 สำคัญ: ทำให้กล่องลูกมีความสูงเท่ากัน */
        flex-wrap: wrap; /* เพื่อให้ตอบสนองต่อหน้าจอขนาดเล็ก */
        min-height: 90vh; /* กำหนดความสูงขั้นต่ำให้ชัดเจน */
    }

    /* ================================================= */
    /* ✅ ส่วน Animation                 */
    /* ================================================= */

    /* 1. Working Border Pulse (สำหรับกล่องหลัก) */
    @keyframes working-pulse {
        0% { box-shadow: 0 0 40px rgba(0,0,0,0.4), 0 0 5px rgba(65, 255, 179, 0.4); }
        50% { box-shadow: 0 0 40px rgba(0,0,0,0.4), 0 0 15px rgba(65, 255, 179, 0.8); }
        100% { box-shadow: 0 0 40px rgba(0,0,0,0.4), 0 0 5px rgba(65, 255, 179, 0.4); }
    }

    /* 2. Loading Spinner */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .loading-spinner::before {
        content: "";
        display: inline-block;
        width: 1.5vw;
        height: 1.5vw;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top-color: #ffac41; /* สีส้ม */
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 10px;
        vertical-align: middle;
    }

    /* ================================================= */
    /* ✅ กล่องข้อมูลหลัก                */
    /* ================================================= */

    .display-container {
        flex: 1 1 600px; 
        background: #1b263b;
        border-radius: 1vw;
        padding: 1.5vw;
        box-shadow: 0 0 40px rgba(0,0,0,0.4);
        display: grid;
        grid-template-rows: auto 1fr auto;
        gap: 1.5vw;
        border: 2px solid #41ffb3;
        transition: box-shadow 0.3s;
        /* เพิ่ม Animation เมื่อสถานะเป็น "Working" */
        animation: working-pulse 3s infinite ease-in-out;
    }

    /* ✅ Header */
    .header h1 {
        font-size: clamp(28px,3.2vw,60px);
        color: #41ffb3;
        text-align: center;
        font-weight: 900;
        text-shadow: 0 0 12px rgba(65,255,179,0.8);
    }

    /* ✅ Grid แสดงข้อมูล */
    .data-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-template-areas: 
            "job machine device"
            "item qty ctn" /* แก้ไขให้ตรงกับจำนวนกล่อง */
            "status status status";
        gap: 1.5vw;
    }

    .data-item, .data-item-highlight {
        background: #2b3a55;
        border-radius: 1vw;
        padding: 1vw;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 12vh;
        border-bottom: 0.5vw solid #ffac41;
        transition: 0.3s;
    }

    .data-item .label, .data-item-highlight .label { font-size: clamp(14px,1.2vw,24px); color:#b8c2d1; margin-bottom:0.5vw; text-transform: uppercase; }
    .data-item .value, .data-item-highlight .value { font-size: clamp(30px,3vw,60px); font-weight:900; color:#fff; }

    /* กำหนด Grid Area ให้กล่องข้อมูลพื้นฐาน */
    .data-grid > .data-item:nth-child(1){ grid-area: job; }
    .data-grid > .data-item:nth-child(2){ grid-area: machine; border-bottom-color:#e94560; }
    .data-grid > .data-item:nth-child(3){ grid-area: device; border-bottom-color:#41ffb3; }

    /* ✅ กล่องข้อมูลที่ต้องการเน้น (Item, QTY, CTN) */
    .data-item-highlight {
        background: linear-gradient(135deg,#2ea34b,#3ed475);
        box-shadow: 0 0 10px rgba(62, 212, 117, 0.5);
        border-bottom: none;
        padding: 1.5vw 1vw;
    }
    /* กำหนด Grid Area ให้กล่องเน้น */
    .data-grid > .data-item-highlight:nth-child(4){ grid-area: item; }
    .data-grid > .data-item-highlight:nth-child(5){ grid-area: qty; border-radius: 1vw; } /* ใช้เป็น qty ไปเลย */
    .data-grid > .data-item-highlight:nth-child(6){ grid-area: ctn; }

    #repeat-item {
        grid-area: status;
        background: #2b3a55;
        color: #ffac41;
        border-radius: 1vw;
        padding: 1vw;
        text-align: center;
        font-size: clamp(18px,2vw,32px);
        font-weight:700;
        transition: 0.3s;
    }

    /* Animation Classes */
    .new-data-flash { animation: flash-bg 0.15s 3 alternate; box-shadow: 0 0 30px rgba(255,255,0,1) !important; }
    .repeat-data-alert { background: #ff4d4d !important; color:#fff !important; animation: pulse-red 1s infinite alternate; }

    @keyframes pulse-red { from{box-shadow:0 0 10px #ff4d4d;} to{box-shadow:0 0 25px #ff4d4d;} }
    @keyframes flash-bg { to{background:#41ffb3;} }

    /* ✅ Footer */
    .footer { text-align:right; font-size: clamp(12px,1vw,20px); color:#b8c2d1; border-top:1px solid rgba(255,255,255,0.1); padding-top:0.5vw; }
    .footer #datetime-value { color:#fff; font-weight:700; }

    /* ================================================= */
    /* ✅ กล่องข้อมูลซ้ำ (ขวา)           */
    /* ================================================= */

    .dup {
        flex: 1 1 400px; 
        display: flex;
        flex-direction: column; 
        gap: 15px;
    }

    #dup-latest {
        flex-shrink: 0; 
        background: linear-gradient(135deg,#ff1e1e,#ff5c5c);
        color: white;
        padding: 1.2vw;
        border-radius: 1vw;
        font-weight: 700;
        font-size: clamp(18px,2vw,36px);
        text-align: center;
        box-shadow: 0 0 30px rgba(255,0,0,0.7);
        animation: pulse-latest 1s infinite alternate;
    }
    @keyframes pulse-latest { from{box-shadow:0 0 15px #ff1e1e;} to{box-shadow:0 0 35px #ff6b6b;} }

    #dup-section {
        flex-grow: 1; 
        background: #2b3a55;
        border-radius: 1vw;
        padding: 1.5vw;
        color: #ff4d4d;
        border: 2px solid rgba(255,77,77,0.5);
        box-shadow: 0 0 20px rgba(255,77,77,0.2);
        display: flex;
        flex-direction: column;
    }

    #dup-section h2 {
        flex-shrink: 0; 
        font-size: clamp(26px,2.5vw,42px);
        color: #ff4d4d;
        text-shadow: 0 0 8px rgba(255,77,77,0.8);
        text-align: center;
        margin-bottom: 1vw;
    }

    #dup-list {
        flex-grow: 1; 
        overflow-y: auto; 
        padding-right: 5px; 
    }

    #dup-list .dup-item {
        background: rgba(255,77,77,0.1);
        border: 1px solid rgba(255,77,77,0.4);
        border-radius: 0.6vw;
        padding: 0.8vw 1vw;
        margin-bottom: 0.5vw;
        font-size: clamp(16px,1.2vw,22px);
        color: #fff;
    }
</style>
</head>
<body>
<div class="main-wrapper"> 

  <div class="display-container">
    <div class="header"><h1 id="mainTitle">📦 Realtime Conveyor Queue</h1></div>
    <div class="data-grid">
      <div class="data-item"><span class="label">Job</span><span class="value" id="job-value">-</span></div>
      <div class="data-item"><span class="label">Machine</span><span class="value" id="machine-value">-</span></div>
      <div class="data-item"><span class="label">Device</span><span class="value" id="device-value">-</span></div>
      
      <div class="data-item-highlight" id="item-item"><span class="label">Item</span><span class="value" id="item-value">-</span></div>
      <div class="data-item-highlight" id="qty-item"><span class="label">QTY</span><span class="value" id="qty-value">-</span></div>
      <div class="data-item-highlight" id="ctn-item"><span class="label">CTN</span><span class="value" id="CTN-value">-</span></div>
      
      <div id="repeat-item" class="loading-spinner">กำลังรอข้อมูลใหม่...</div>
    </div>
    <div class="footer">เวลาอัพเดตล่าสุด: <span id="datetime-value">-</span></div>
  </div>

  <div class="dup">
    <div id="dup-latest">🚨 ยังไม่มีค่าซ้ำล่าสุด</div>

    <div id="dup-section">
      <h2>⚠️ Serial Lot ที่มาซ้ำ</h2>
      <div id="dup-list"><div class="dup-item">ยังไม่มีประวัติข้อมูลซ้ำ</div></div>
    </div>
  </div>

</div>

<script>
let lastId = 0, initialLoad = true, isAlerting = false;

// Function ที่จะเอามาใช้ร่วมกันในการจัดการ Class และ Animation
function updateStatusDisplay(isLoading = true, isRepeat = false) {
    const repeatItem = document.getElementById("repeat-item");
    const container = document.querySelector(".display-container");

    // 1. จัดการ Animation Working Pulse ที่กล่องหลัก
    if (isLoading || isRepeat) {
        container.style.animationPlayState = 'running';
    } else {
        container.style.animationPlayState = 'paused';
        container.style.boxShadow = '0 0 40px rgba(0,0,0,0.4), 0 0 5px rgba(65, 255, 179, 0.4)'; // Set default shadow
    }

    // 2. จัดการ Status Box และ Loading Spinner
    repeatItem.classList.remove('repeat-data-alert', 'loading-spinner');
    if (isLoading && !isRepeat) {
        repeatItem.classList.add('loading-spinner');
        repeatItem.textContent = "กำลังเชื่อมต่อและรอข้อมูลใหม่...";
    } else if (isRepeat) {
        repeatItem.classList.add('repeat-data-alert');
        repeatItem.textContent = "🚨 ข้อมูลล่าสุดยังเป็นตัวเดิม (ID: " + lastId + ")";
    } else {
        repeatItem.textContent = "✅ สถานะ: ปกติ | รอข้อมูลถัดไป...";
    }
}


// ✅ ดึงข้อมูลปกติ
function fetchLatest() {
    updateStatusDisplay(true); // เริ่มต้นด้วยสถานะ Loading

    fetch(`Con_disfetch_latest.php?last_id=${lastId}`)
        .then(res => res.json())
        .then(data => {
            const title = document.getElementById("mainTitle");
            const repeatItem = document.getElementById("repeat-item");
            const flashItems = document.querySelectorAll('.data-item-highlight'); // Flash ทั้ง Item, QTY, CTN

            if (data.error) {
                updateStatusDisplay(false, false);
                repeatItem.textContent = "⚠️ ไม่มีข้อมูลในระบบ";
                title.textContent = "⚠️ รอข้อมูล...";
                return;
            }

            if (data.id && (data.id > lastId || initialLoad)) {
                // --- ข้อมูลใหม่มาถึง ---
                lastId = data.id;
                initialLoad = false;
                isAlerting = false;
                
                // 1. อัปเดตค่า
                document.getElementById("job-value").textContent = data.job || '-';
                document.getElementById("machine-value").textContent = data.machine || '-';
                document.getElementById("device-value").textContent = data.device || '-';
                document.getElementById("item-value").textContent = data.item || '-';
                document.getElementById("qty-value").textContent = data.qty || '-';
                document.getElementById("CTN-value").textContent = data.CTN || '-';
                document.getElementById("datetime-value").textContent = data.datetime || new Date().toLocaleTimeString('th-TH');
                
                // 2. แสดง Animation
                updateStatusDisplay(false, false); // หยุด loading
                repeatItem.textContent = "🚀 ข้อมูลใหม่ถูกดึงเข้าสู่ระบบ!";
                title.textContent = `⚡️ ข้อมูลใหม่ล่าสุด (ID: ${lastId})`;

                flashItems.forEach(item => item.classList.add('new-data-flash'));
                
                setTimeout(() => {
                    flashItems.forEach(item => item.classList.remove('new-data-flash'));
                    title.textContent = "✅ Realtime Conveyor Queue";
                    updateStatusDisplay(false, false);
                }, 1000);

            } else if (data.repeat === true) {
                // --- ข้อมูลซ้ำ/ตัวเดิม ---
                isAlerting = true;
                updateStatusDisplay(false, true); // แสดงสถานะ Repeat Alert
                title.textContent = "⏳ รอข้อมูลใหม่...";
            } else {
                // --- สถานะปกติ ไม่มีการเปลี่ยนแปลง ---
                updateStatusDisplay(false, false);
                title.textContent = "✅ Realtime Conveyor Queue";
            }
        })
        .catch(err => {
            console.error("Fetch error:", err);
            updateStatusDisplay(false, false);
            document.getElementById("repeat-item").textContent = "❌ เกิดข้อผิดพลาดในการเชื่อมต่อ";
        });
}

// ✅ ดึงข้อมูลซ้ำ (ไม่มีการเปลี่ยนแปลงในฟังก์ชันนี้)
function fetchDup() {
    fetch("Con_disfetch_latest_dup.php")
        .then(res => res.json())
        .then(data => {
            const latestBox = document.getElementById("dup-latest");
            const dupList = document.getElementById("dup-list");

            latestBox.innerHTML = "";
            dupList.innerHTML = "";

            if (data.latest) {
                const latest = data.latest;
                latestBox.innerHTML = `🚨 <strong>${latest.QRcode}</strong> | 📟 ${latest.device} | 🕒 ${new Date(latest.seen_at).toLocaleTimeString('th-TH')}`;
            } else {
                latestBox.innerHTML = "✅ ไม่มีค่าซ้ำล่าสุด";
            }

            if (data.history && data.history.length > 0) {
                data.history.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "dup-item";
                    div.innerHTML = `⚠️ <strong>${item.QRcode}</strong> | 📟 ${item.device} | 🕒 ${new Date(item.seen_at).toLocaleTimeString('th-TH')}`;
                    dupList.appendChild(div);
                });
            } else {
                dupList.innerHTML = "<div class='dup-item'>✅ ไม่มีข้อมูลซ้ำในประวัติ</div>";
            }
        })
        .catch(err => console.error("Error fetching dup:", err));
}

// เริ่มการทำงาน
setInterval(fetchLatest, 1000);
fetchLatest();
setInterval(fetchDup, 1000);
fetchDup();
</script>
</body>
</html>