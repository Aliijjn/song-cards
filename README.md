# Song Cards

<img width="3418" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/bd5a2c61-c329-45ce-a0ff-82c29af56411" />


## How the application works
(note: background gradient doesn't work because Firefox)
### 1. Make sure you're logged in through Spotify. Go to "Curations" in the top-right and click "Create Curation"
<img width="3418" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/f44c3c73-f95a-4ae0-a1ba-162195873c1e" />


### 2. Give your curation a name and select the playlists you want to include (can be multiple)
<img width="3008" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/f62d3ea1-4ae8-44e3-bc85-25bc5b1e439c" />

### 3. [Optional] Go to "Song Details" and check if the data is correct. You can edit a song by pressing the pencil. Spotify's API can be a bit of a mess with singles and rereleases
<img width="3418" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/62f79898-1bec-41ff-ab63-569ae83020eb" />

<img width="3418" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/ff58f40f-14a9-4453-b9e5-8b2913e517e1" />

### 4. [Optional] Go to "Advanced Options". Here you can make copies of and combine curations
<img width="3418" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/40f7abf8-1ded-4bab-b940-8a79b805aac3" />

<img width="3418" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/a85aceec-1d58-4cd1-b283-ff8884eb595a" />


### 5. In "Advanced Options", click on "Export" to export your curation to a PDF. Beware, this takes quite a while, especially if your curation is large (>100 songs)

### 6. Enjoy your cards!
<img width="3418" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/92247fa0-e4e9-4dc0-a895-3c7bad8563f0" />

<img width="3418" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/bd5a2c61-c329-45ce-a0ff-82c29af56411" />


## Setup
1. Clone the project
2. [Make a Spotify developer account](https://developer.spotify.com/) and set the callback to:
```
https://127.0.0.1:8001/api/callback
```
3. In the backend (`app-back-end`), set the following variables in your env
```
SPOTIFY_CLIENT_ID=REDACTED
SPOTIFY_CLIENT_SECRET=NOT_LEAKING_MY_KEYS_🤡
FRONTEND_URL=http://localhost:5173
```
4. In the backend (`app-back-end`), for the setup run:
```
composer install
php artisan migrate
```
Paste this into the user table:
```
1,Test User,test,2026-04-04 00:20:32.000,1234,1234,2026-04-04 00:20:39.000,2026-04-29 19:40:37,"","",
```
To start the server, run:
```
symfony serve --port=8001
```
5. In the frontend run:
```
npm install
```
To start the server, run:
```
npm run dev
```
