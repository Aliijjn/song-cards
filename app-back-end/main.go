package main

import (
	"encoding/json"
	"log"
	"net/http"
	"os"
	"strings"
	"path"
	"fmt"
)

type Song struct {
	Title  string `json:"title"`
	Artist string `json:"artist"`
	Album  string `json:"album"`
	Length int32  `json:"song_duration"`
	Year   int32  `json:"release_date"`
	Views  int64  `json:"total_views_on_spotify"`
}

func getSongData(w http.ResponseWriter, r *http.Request) {
	data, err := os.ReadFile("songs.json")
	if err != nil {
		log.Fatal(err)
	}

	var songs []Song
	if err := json.Unmarshal(data, &songs); err != nil {
		log.Fatal(err)
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(songs)
}

func getAlbumCover(w http.ResponseWriter, r *http.Request) {
	// Extract album name
	name := strings.TrimPrefix(r.URL.Path, "/api/album-cover/")
	name = path.Clean(name) // sanitize

	// Optional: block paths like "../", 'home or macros
	if strings.ContainsAny(name, ".~$") {
		http.Error(w, "Invalid path", http.StatusBadRequest)
		return
	}

	w.Header().Set("Content-Type", "image/png")
	filePath := fmt.Sprintf("images/%s.png", name)
	_, err := os.Stat(filePath)
	if os.IsNotExist(err) {
			filePath = "images/default.png"
	}
	http.ServeFile(w, r, filePath)
}

func withCORS(handler http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Access-Control-Allow-Origin", "*")
		handler(w, r)
	}
}

func main() {
	http.HandleFunc("/api/songs", withCORS(getSongData));
	http.HandleFunc("/api/album-cover/", withCORS(getAlbumCover));

	// http.HandleFunc("/api/images", withCORS(func(w http.ResponseWriter, r *http.Request) {
	// 	data, err := os.ReadFile("songs.json")
	// }))

	log.Println("Server started on :8080")
	log.Fatal(http.ListenAndServe(":8080", nil))
}