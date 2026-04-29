# Song Cards



## How the application works
1. Make sure you're logged in through Spotify. Go to "Curations" in the top-right and click "Create Curation"
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/46423851-a898-4989-b0bc-960e254dfd67" />

2. Give your curation a name and select the playlists you want to include (can be multiple)
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/4f9478fa-856c-4dd7-ad5c-8fc89dcb1438" />

3. [Optional] Go to "Song Details" and check if the data is correct. You can edit a song by pressing the pencil. Spotify's API can be a bit of a mess with singles and rereleases
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/2ab89f05-e9fb-4d19-8a85-d0464665b5e2" />

<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/b29bc9fb-1c57-45fb-8ca6-0700ca41f6a6" />

4. Go to "Advanced Options" and click on "Export" to export your curation to a PDF. Beware, this takes quite a while, especially if your curation is large (>100 songs)
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/b0bac8df-0074-422d-9a53-87acdc394e0f" />

5. Enjoy your cards!
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/74b401c3-cd87-450b-887c-0d79e4a6d33f" />

<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/ac4bf1d9-0058-43b1-99ea-a1c230f1b7a9" />

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
