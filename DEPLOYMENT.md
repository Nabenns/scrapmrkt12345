# Deployment Guide (VPS)

Follow these steps to deploy the MRKT Scraper to your new VPS.

## 1. Prepare the VPS
Connect to your VPS via SSH. We recommend **Ubuntu 22.04 LTS**.

### Install Docker & Docker Compose
```bash
# Add Docker's official GPG key:
sudo apt-get update
sudo apt-get install ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

# Add the repository to Apt sources:
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update

# Install Docker packages:
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Verify installation:
sudo docker run hello-world
```

## 2. Transfer Project Files
You can transfer the files from your local machine to the VPS using `scp` (run this from your local machine):

```bash
# Replace user@your-vps-ip with your actual VPS details
scp -r c:/Users/ben/antigravity/mrkt-scraper user@your-vps-ip:~/mrkt-scraper
```
*Alternatively, if you pushed this to GitHub, just `git clone` it on the VPS.*

## 3. Configure Environment
SSH into your VPS and go to the project folder:
```bash
cd ~/mrkt-scraper
```

Ensure your `.env` file is present and correct. If you copied the folder, it should be there. If not:
```bash
cp .env.example .env
nano .env
# Edit database credentials if needed
```

## 4. Start the Application
Build and start the containers:
```bash
sudo docker compose up -d --build
```

## 5. Configure Authentication
Once running, the scraper needs the Auth Token.

1.  Open your browser and go to: `http://<YOUR_VPS_IP>:8000/admin`
2.  Paste the **Local Storage JSON** from `app.mrktedge.ai`.
3.  Click **Update Token**.

## 6. Verify
Check the logs to ensure the scraper is working:
```bash
sudo docker compose logs -f scraper
```

## 7. Domain Setup (api.bensserver.cloud)

To make your API accessible at `https://api.bensserver.cloud`, follow these steps.

### Option A: Cloudflare (Easiest & Free SSL)
1.  **Add Site**: Add `bensserver.cloud` to your Cloudflare account.
2.  **DNS**: Create an **A Record**:
    *   Name: `api`
    *   Content: `<YOUR_VPS_IP>`
    *   Proxy status: **Proxied (Orange Cloud)**
3.  **SSL**: Go to **SSL/TLS** -> **Overview** and set encryption mode to **Flexible** (since our Docker container listens on port 80).
4.  **Access**: Your API is now available at `https://api.bensserver.cloud/api/headlines`.

### Option B: Nginx Proxy Manager (Advanced)
If you are not using Cloudflare, you can install Nginx Proxy Manager on your VPS to handle SSL.

1.  Install Nginx Proxy Manager (via Docker).
2.  Login to the Admin UI (usually port 81).
3.  Add a **Proxy Host**:
    *   Domain Names: `api.bensserver.cloud`
    *   Scheme: `http`
    *   Forward Hostname: `localhost` (or the Docker IP)
    *   Forward Port: `8000`
4.  **SSL Tab**: Request a new Let's Encrypt certificate.

