// ================================
// CANVAS SYNC - POLLING MODE
// ================================

console.log("✓ canvas-sync.js loaded");

const TabletCanvas = {
    canvas: null,
    ctx: null,
    isDrawing: false,
    sessionId: null,

    init(sessionId) {
        console.log("🎨 TabletCanvas init:", sessionId);
        this.sessionId = sessionId;
        this.canvas = document.getElementById("signature_canvas");

        if (!this.canvas) {
            console.error("❌ Canvas not found");
            return;
        }

        this.ctx = this.canvas.getContext("2d");
        this.setupCanvas();
        this.attachEvents();
        console.log("✓ TabletCanvas ready");
    },

    setupCanvas() {
        const rect = this.canvas.getBoundingClientRect();
        const ratio = window.devicePixelRatio || 1;

        this.canvas.width = rect.width * ratio;
        this.canvas.height = rect.height * ratio;
        this.ctx.scale(ratio, ratio);

        this.ctx.fillStyle = "#ffffff";
        this.ctx.fillRect(0, 0, rect.width, rect.height);
        this.ctx.strokeStyle = "#000000";
        this.ctx.lineWidth = 2;
        this.ctx.lineCap = "round";
    },

    attachEvents() {
        console.log("📌 Attaching events...");
        this.canvas.addEventListener("mousedown", (e) => this.startDraw(e));
        this.canvas.addEventListener("mousemove", (e) => this.draw(e));
        this.canvas.addEventListener("mouseup", () => this.stopDraw());
        this.canvas.addEventListener("mouseout", () => this.stopDraw());
    },

    startDraw(e) {
        this.isDrawing = true;
        const pos = this.getMousePos(e);
        this.ctx.beginPath();
        this.ctx.moveTo(pos.x, pos.y);
    },

    draw(e) {
        if (!this.isDrawing) return;
        const pos = this.getMousePos(e);
        this.ctx.lineTo(pos.x, pos.y);
        this.ctx.stroke();
    },

    stopDraw() {
        if (this.isDrawing) {
            this.ctx.closePath();
            this.isDrawing = false;
        }
    },

    getMousePos(e) {
        const rect = this.canvas.getBoundingClientRect();
        const ratio = window.devicePixelRatio || 1;
        return {
            x: (e.clientX - rect.left) / ratio,
            y: (e.clientY - rect.top) / ratio,
        };
    },

    clear() {
        const rect = this.canvas.getBoundingClientRect();
        this.ctx.fillStyle = "#ffffff";
        this.ctx.fillRect(0, 0, rect.width, rect.height);
    },

    getBase64() {
        return this.canvas.toDataURL("image/png");
    },
};

const LaptopCanvas = {
    canvas: null,
    ctx: null,
    lastSignatureData: null,
    pollingInterval: null,

    init(sessionId) {
        console.log("🖥️ LaptopCanvas init:", sessionId);
        this.canvas = document.getElementById("mirror_canvas");

        if (!this.canvas) {
            console.error("❌ Mirror canvas not found");
            return;
        }

        this.ctx = this.canvas.getContext("2d");
        this.setupCanvas();
        this.startPolling(sessionId);
        console.log("✓ LaptopCanvas ready (polling mode)");
    },

    setupCanvas() {
        const rect = this.canvas.getBoundingClientRect();
        const ratio = window.devicePixelRatio || 1;

        this.canvas.width = rect.width * ratio;
        this.canvas.height = rect.height * ratio;
        this.ctx.scale(ratio, ratio);

        this.ctx.fillStyle = "#ffffff";
        this.ctx.fillRect(0, 0, rect.width, rect.height);
        this.ctx.strokeStyle = "#000000";
        this.ctx.lineWidth = 2;
    },

    startPolling(sessionId) {
    console.log('🔄 Starting polling for session:', sessionId);
    
    this.pollingInterval = setInterval(async () => {
        try {
            const response = await fetch(`/api/cek-ttd-petugas/${sessionId}`);
            const data = await response.json();

            // Jika TTD sudah ada
            if (data && data.sudah_ttd) {
                console.log('✅ TTD detected! Fetching full data...');
                
                // Fetch full tamu data untuk dapat path signature
                const tamuResponse = await fetch(`/api/tamu/${sessionId}`);
                const tamuData = await tamuResponse.json();
                
                if (tamuData && tamuData.tanda_tangan && tamuData.tanda_tangan !== this.lastSignatureData) {
                    console.log('📸 Signature path:', tamuData.tanda_tangan);
                    this.displaySignature(tamuData.tanda_tangan);
                    this.lastSignatureData = tamuData.tanda_tangan;
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }, 500); // Faster polling - setiap 500ms
},

displaySignature(signaturePath) {
    console.log('🎨 Displaying signature from path:', signaturePath);
    
    // Clear canvas dulu
    this.ctx.fillStyle = '#ffffff';
    this.ctx.fillRect(0, 0, this.canvas.offsetWidth, this.canvas.offsetHeight);
    
    const img = new Image();
    const imageUrl = `/storage/${signaturePath}?t=${Date.now()}`;
    
    console.log('📷 Loading image from:', imageUrl);
    
    img.onload = () => {
        console.log('✓ Image loaded, drawing to canvas');
        this.ctx.drawImage(img, 0, 0, this.canvas.offsetWidth, this.canvas.offsetHeight);
        
        // Juga display di image element
        const imgElement = document.getElementById('received-signature-img');
        if (imgElement) {
            imgElement.src = imageUrl;
            const container = document.getElementById('received-signature-container');
            if (container) {
                container.classList.remove('hidden');
            }
        }
    };
    
    img.onerror = (err) => {
        console.error('❌ Failed to load image:', err);
        console.error('URL:', imageUrl);
    };
    
    img.src = imageUrl;
},

    captureSignature() {
        const base64 = this.canvas.toDataURL("image/png");
        const input = document.getElementById("signature_base64");
        if (input) {
            input.value = base64;
            console.log("✓ Signature captured");
            return true;
        }
        return false;
    },

    clear() {
        const rect = this.canvas.getBoundingClientRect();
        this.ctx.fillStyle = "#ffffff";
        this.ctx.fillRect(0, 0, rect.width, rect.height);
        this.lastSignatureData = null;
    },

    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            console.log("🛑 Polling stopped");
        }
    },

    // Di dalam LaptopCanvas object, tambahkan method baru:

    displayLiveSignature(signaturePath) {
        console.log("🎨 Displaying signature:", signaturePath);

        const img = new Image();
        img.onload = () => {
            // Clear canvas dulu
            this.ctx.fillStyle = "#ffffff";
            this.ctx.fillRect(
                0,
                0,
                this.canvas.offsetWidth,
                this.canvas.offsetHeight,
            );

            // Draw image
            this.ctx.drawImage(
                img,
                0,
                0,
                this.canvas.offsetWidth,
                this.canvas.offsetHeight,
            );
            console.log("✓ Signature displayed on mirror canvas");
        };
        img.onerror = () => {
            console.error("❌ Failed to load signature");
        };
        img.src = `/storage/${signaturePath}?t=${Date.now()}`;
    },
};

// ================================
// EXPORT to global window scope
// ================================
window.TabletCanvas = TabletCanvas;
window.LaptopCanvas = LaptopCanvas;

console.log("✓ Canvas objects exported");

// Signal ready
document.dispatchEvent(
    new CustomEvent("canvasSyncReady", {
        detail: { TabletCanvas, LaptopCanvas },
    }),
);

console.log("✓ canvas-sync.js ready");
