# Dailymotion Stream URL Generation Sample

This stream URL generation samples is using third party player.

- VideoJS
- HLS Player

## Getting started

1. PHP 8.2
2. Apache/Nginx
3. Composer

### CLI steps

```bash
git clone git@github.com:Dailymotion-Pro-Services/Stream-URL.git
cd Stream-URL
cp .env-example .env
composer install
```

## Generate API Key and API Secret

You can go to your Dailymotion Studio to create the API key. Choose Private API Key. Copy and paste them to .env file

```
API_KEY="your-api-key"
API_SECRET="your-api-secret"
ALLOWED_DOMAIN="https://your-allowed.domain,https://your-friends.domain" // You can put as many as you need
LOCAL_IP="x.x.x.x" // You need this for local test, if don't need just leave it blank
```

## Implementation

We have 3 files in public folder

- index.html is one sample of how to use the player
- videojs.html is the implementation VideoJS player
- hls.html is the implementation HLS player

### Anatomy

To use the player load which player you want to use. For the example below is VideoJS. In case of using HLS player. It's also has the same anatomy. 

```html
<iframe src="https://your-domain.com/videojs.html?video_id=x9alkna&videoformats=stream_h264_hq_url" title="Stream URL Video" allow="autoplay; fullscreen"></iframe>
```

#### Steps
1. Load `videojs.html`
2. Add some query parameters `?video_id=xxxxx&videoformats=stream_hls_url`
   - Video ID from Dailymotion
   - Video Format. [List of available video formats](https://developers.dailymotion.com/guides/generate-stream-urls/#hls-manifests)

#### Customization

You can also use another third party player with the same pattern we provided. Just need some adaptation depends on the third party player.

## Production

To deploy in production please pointing the root folder to **public** folder
