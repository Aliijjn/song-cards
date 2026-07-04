# Song Cards
With this application, you can transform your Spotify playlists into your own song cards (inspired by the game Hitster)
All you need is a Spotify account, a printer and a pair of scissors!

<img width="4080" height="2654" alt="IMG_20260606_230604432_1" src="https://github.com/user-attachments/assets/c893bbe9-de89-40d6-a627-4cdf4250cb30" />

## How the application works
(note: background gradient doesn't work because Firefox)
> 1. Make sure you're logged in through Spotify. Go to "Curations" in the top-right and click "Create Curation"
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/f3cbc4dc-a8d7-4091-9709-7110b7711611" />

> 2. Give your curation a name and select the playlists you want to include (can be multiple)
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/908fd1ff-04e0-467a-bd9b-c5c4130ed15d" />

> 3. [Optional] Go to "Song Details" and check if the data is correct. You can edit a song by pressing the pencil. Spotify's API can be a bit of a mess with singles and rereleases
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/c59b9fc2-f745-44de-946f-2cc7bdb0cd4d" />

<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/b8351e06-d92b-4334-a5a8-2221232113fa" />

> 4. [Optional] Go to "Advanced Options". Here you can make copies of your curation and combine it with other curations
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/28cb5869-2b41-4007-b357-c64a89121961" />

<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/83c600b6-1b56-4aa3-a2ff-a3ca6c61a40f" />

> 5. In "Advanced Options", click on "Export" to export your curation to a PDF
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/0fdefc85-f8fd-46e7-ae34-3aa7d093807e" />

> 6. Enjoy your cards!
<img width="3420" height="1968" alt="afbeelding" src="https://github.com/user-attachments/assets/c5a74118-3ca3-4794-9139-38b1ef20978f" />

<img width="720" height="1811" alt="afbeelding" src="https://github.com/user-attachments/assets/b37abf07-2ef6-4ed9-9efb-10302ea1c89a" />





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
