<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara
        Schema::disableForeignKeyConstraints();

        // Kosongkan tabel sebelum melakukan seed
        DB::table('movies')->truncate();

        // Data movie
        $movies = [
            [
                'title' => 'The Shawshank Redemption',
                'slug' => Str::slug('The Shawshank Redemption'),
                'description' => 'Dua pria yang dipenjara menjalin persahabatan selama bertahun-tahun.',
                'director' => 'Frank Darabont',
                'writers' => 'Stephen King, Frank Darabont',
                'stars' => 'Tim Robbins, Morgan Freeman',
                'poster' => 'https://m.media-amazon.com/images/I/51NiGlapXlL._AC_.jpg',
                'release_date' => '1994-09-22',
                'duration' => 142,
                'url_720' => 'https://example.com/shawshank_720.mp4',
                'url_1080' => 'https://example.com/shawshank_1080.mp4',
                'url_4k' => 'https://example.com/shawshank_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Godfather',
                'slug' => Str::slug('The Godfather'),
                'description' => 'Patriark yang menua dari dinasti kejahatan terorganisir menyerahkan kendali kepada putranya.',
                'director' => 'Francis Ford Coppola',
                'writers' => 'Mario Puzo, Francis Ford Coppola',
                'stars' => 'Marlon Brando, Al Pacino',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BYTJkNGQyZDgtZDQ0NC00MDM0LWEzZWQtYzUzZDEwMDljZWNjXkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '1972-03-24',
                'duration' => 175,
                'url_720' => 'https://example.com/godfather_720.mp4',
                'url_1080' => 'https://example.com/godfather_1080.mp4',
                'url_4k' => 'https://example.com/godfather_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Dark Knight',
                'slug' => Str::slug('The Dark Knight'),
                'description' => 'Batman menghadapi ujian psikologis terbesar dari Joker.',
                'director' => 'Christopher Nolan',
                'writers' => 'Jonathan Nolan, Christopher Nolan',
                'stars' => 'Christian Bale, Heath Ledger',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_SX300.jpg',
                'release_date' => '2008-07-18',
                'duration' => 152,
                'url_720' => 'https://example.com/dark_knight_720.mp4',
                'url_1080' => 'https://example.com/dark_knight_1080.mp4',
                'url_4k' => 'https://example.com/dark_knight_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Pulp Fiction',
                'slug' => Str::slug('Pulp Fiction'),
                'description' => 'Kehidupan dua pembunuh bayaran, seorang petinju, dan seorang gangster saling terkait.',
                'director' => 'Quentin Tarantino',
                'writers' => 'Quentin Tarantino, Roger Avary',
                'stars' => 'John Travolta, Uma Thurman',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BYTViYTE3ZGQtNDBlMC00ZTAyLTkyODMtZGRiZDg0MjA2YThkXkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '1994-10-14',
                'duration' => 154,
                'url_720' => 'https://example.com/pulp_fiction_720.mp4',
                'url_1080' => 'https://example.com/pulp_fiction_1080.mp4',
                'url_4k' => 'https://example.com/pulp_fiction_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Forrest Gump',
                'slug' => Str::slug('Forrest Gump'),
                'description' => 'Kisah Forrest Gump, seorang pria dengan hati yang baik.',
                'director' => 'Robert Zemeckis',
                'writers' => 'Winston Groom, Eric Roth',
                'stars' => 'Tom Hanks, Robin Wright',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BNDYwNzVjMTItZmU5YS00YjQ5LTljYjgtMjY2NDVmYWMyNWFmXkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '1994-07-06',
                'duration' => 142,
                'url_720' => 'https://example.com/forrest_gump_720.mp4',
                'url_1080' => 'https://example.com/forrest_gump_1080.mp4',
                'url_4k' => 'https://example.com/forrest_gump_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Inception',
                'slug' => Str::slug('Inception'),
                'description' => 'Seorang pencuri yang memiliki kemampuan memasuki mimpi orang lain ditawari kesempatan untuk menghapus catatan kriminalnya.',
                'director' => 'Christopher Nolan',
                'writers' => 'Christopher Nolan',
                'stars' => 'Leonardo DiCaprio, Joseph Gordon-Levitt',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_SX300.jpg',
                'release_date' => '2010-07-16',
                'duration' => 148,
                'url_720' => 'https://example.com/inception_720.mp4',
                'url_1080' => 'https://example.com/inception_1080.mp4',
                'url_4k' => 'https://example.com/inception_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Fight Club',
                'slug' => Str::slug('Fight Club'),
                'description' => 'Seorang pekerja kantoran yang tidak puas dan seorang pembuat sabun membentuk klub pertarungan bawah tanah.',
                'director' => 'David Fincher',
                'writers' => 'Chuck Palahniuk, Jim Uhls',
                'stars' => 'Brad Pitt, Edward Norton',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BOTgyOGQ1NDItNGU3Ny00MjU3LTg2YWEtNmEyYjBiMjI1Y2M5XkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '1999-10-15',
                'duration' => 139,
                'url_720' => 'https://example.com/fight_club_720.mp4',
                'url_1080' => 'https://example.com/fight_club_1080.mp4',
                'url_4k' => 'https://example.com/fight_club_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Matrix',
                'slug' => Str::slug('The Matrix'),
                'description' => 'Seorang programmer komputer menemukan kenyataan bahwa hidupnya hanyalah simulasi komputer.',
                'director' => 'Lana Wachowski, Lilly Wachowski',
                'writers' => 'Lana Wachowski, Lilly Wachowski',
                'stars' => 'Keanu Reeves, Laurence Fishburne',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BN2NmN2VhMTQtMDNiOS00NDlhLTliMjgtODE2ZTY0ODQyNDRhXkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '1999-03-31',
                'duration' => 136,
                'url_720' => 'https://example.com/the_matrix_720.mp4',
                'url_1080' => 'https://example.com/the_matrix_1080.mp4',
                'url_4k' => 'https://example.com/the_matrix_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Interstellar',
                'slug' => Str::slug('Interstellar'),
                'description' => 'Sebuah tim penjelajah melakukan perjalanan melalui lubang cacing untuk memastikan kelangsungan hidup manusia.',
                'director' => 'Christopher Nolan',
                'writers' => 'Jonathan Nolan, Christopher Nolan',
                'stars' => 'Matthew McConaughey, Anne Hathaway',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BYzdjMDAxZGItMjI2My00ODA1LTlkNzItOWFjMDU5ZDJlYWY3XkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '2014-11-07',
                'duration' => 169,
                'url_720' => 'https://example.com/interstellar_720.mp4',
                'url_1080' => 'https://example.com/interstellar_1080.mp4',
                'url_4k' => 'https://example.com/interstellar_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Gladiator',
                'slug' => Str::slug('Gladiator'),
                'description' => 'Seorang jenderal Romawi menjadi budak dan berusaha membalas dendam pada kaisar yang menghancurkan keluarganya.',
                'director' => 'Ridley Scott',
                'writers' => 'David Franzoni, John Logan',
                'stars' => 'Russell Crowe, Joaquin Phoenix',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BYWQ4YmNjYjEtOWE1Zi00Y2U4LWI4NTAtMTU0MjkxNWQ1ZmJiXkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '2000-05-05',
                'duration' => 155,
                'url_720' => 'https://example.com/gladiator_720.mp4',
                'url_1080' => 'https://example.com/gladiator_1080.mp4',
                'url_4k' => 'https://example.com/gladiator_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Lord of the Rings: The Fellowship of the Ring',
                'slug' => Str::slug('The Lord of the Rings: The Fellowship of the Ring'),
                'description' => 'Seorang Hobbit muda memulai perjalanan untuk menghancurkan cincin yang sangat kuat.',
                'director' => 'Peter Jackson',
                'writers' => 'J.R.R. Tolkien, Fran Walsh',
                'stars' => 'Elijah Wood, Ian McKellen',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BZmI4ZmIxOGQtMGY2ZS00Y2Y5LTllMDItYzllOWFmMTNlMmY2XkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '2001-12-19',
                'duration' => 178,
                'url_720' => 'https://example.com/lotr_fellowship_720.mp4',
                'url_1080' => 'https://example.com/lotr_fellowship_1080.mp4',
                'url_4k' => 'https://example.com/lotr_fellowship_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Avengers: Endgame',
                'slug' => Str::slug('Avengers: Endgame'),
                'description' => 'Para Avengers yang tersisa berjuang untuk membalikkan kehancuran yang disebabkan oleh Thanos.',
                'director' => 'Anthony Russo, Joe Russo',
                'writers' => 'Christopher Markus, Stephen McFeely',
                'stars' => 'Robert Downey Jr., Chris Evans',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg',
                'release_date' => '2019-04-26',
                'duration' => 181,
                'url_720' => 'https://example.com/endgame_720.mp4',
                'url_1080' => 'https://example.com/endgame_1080.mp4',
                'url_4k' => 'https://example.com/endgame_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Parasite',
                'slug' => Str::slug('Parasite'),
                'description' => 'Kisah dua keluarga yang kehidupan mereka menjadi terjalin dengan cara yang tidak terduga.',
                'director' => 'Bong Joon-ho',
                'writers' => 'Bong Joon-ho, Han Jin-won',
                'stars' => 'Song Kang-ho, Lee Sun-kyun',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BYjk1Y2U4MjQtY2ZiNS00OWQyLWI3MmYtZWUwNmRjYWRiNWNhXkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '2019-05-30',
                'duration' => 132,
                'url_720' => 'https://example.com/parasite_720.mp4',
                'url_1080' => 'https://example.com/parasite_1080.mp4',
                'url_4k' => 'https://example.com/parasite_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Whiplash',
                'slug' => Str::slug('Whiplash'),
                'description' => 'Seorang drummer muda menghadapi tantangan emosional di bawah pelatihan instruktur yang keras.',
                'director' => 'Damien Chazelle',
                'writers' => 'Damien Chazelle',
                'stars' => 'Miles Teller, J.K. Simmons',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMmNkODhkYjctMDMyOC00ZTNjLTkwZTItM2ExMTAxMGU1ZGQ1XkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '2014-10-10',
                'duration' => 106,
                'url_720' => 'https://example.com/whiplash_720.mp4',
                'url_1080' => 'https://example.com/whiplash_1080.mp4',
                'url_4k' => 'https://example.com/whiplash_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Django Unchained',
                'slug' => Str::slug('Django Unchained'),
                'description' => 'Seorang pemburu hadiah membebaskan seorang budak untuk membantunya menangkap penjahat.',
                'director' => 'Quentin Tarantino',
                'writers' => 'Quentin Tarantino',
                'stars' => 'Jamie Foxx, Christoph Waltz',
                'poster' => 'https://m.media-amazon.com/images/I/91xXtRhtH7L._AC_SL1500_.jpg',
                'release_date' => '2012-12-25',
                'duration' => 165,
                'url_720' => 'https://example.com/django_720.mp4',
                'url_1080' => 'https://example.com/django_1080.mp4',
                'url_4k' => 'https://example.com/django_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Prestige',
                'slug' => Str::slug('The Prestige'),
                'description' => 'Dua pesulap saingan terlibat dalam persaingan mematikan untuk menciptakan trik terbaik.',
                'director' => 'Christopher Nolan',
                'writers' => 'Jonathan Nolan, Christopher Nolan',
                'stars' => 'Christian Bale, Hugh Jackman',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMjA4NDI0MTIxNF5BMl5BanBnXkFtZTYwNTM0MzY2._V1_SX300.jpg',
                'release_date' => '2006-10-20',
                'duration' => 130,
                'url_720' => 'https://example.com/prestige_720.mp4',
                'url_1080' => 'https://example.com/prestige_1080.mp4',
                'url_4k' => 'https://example.com/prestige_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Social Network',
                'slug' => Str::slug('The Social Network'),
                'description' => 'Kisah pendirian Facebook dan perseteruan di baliknya.',
                'director' => 'David Fincher',
                'writers' => 'Aaron Sorkin, Ben Mezrich',
                'stars' => 'Jesse Eisenberg, Andrew Garfield',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMjlkNTE5ZTUtNGEwNy00MGVhLThmZjMtZjU1NDE5Zjk1NDZkXkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '2010-10-01',
                'duration' => 120,
                'url_720' => 'https://example.com/social_network_720.mp4',
                'url_1080' => 'https://example.com/social_network_1080.mp4',
                'url_4k' => 'https://example.com/social_network_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Mad Max: Fury Road',
                'slug' => Str::slug('Mad Max: Fury Road'),
                'description' => 'Dalam gurun pasca-apokaliptik, seorang pemberontak bergabung dengan wanita pejuang untuk melarikan diri dari tirani.',
                'director' => 'George Miller',
                'writers' => 'George Miller, Brendan McCarthy',
                'stars' => 'Tom Hardy, Charlize Theron',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BZDRkODJhOTgtOTc1OC00NTgzLTk4NjItNDgxZDY4YjlmNDY2XkEyXkFqcGc@._V1_SX300.jpg',
                'release_date' => '2015-05-15',
                'duration' => 120,
                'url_720' => 'https://example.com/mad_max_720.mp4',
                'url_1080' => 'https://example.com/mad_max_1080.mp4',
                'url_4k' => 'https://example.com/mad_max_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'La La Land',
                'slug' => Str::slug('La La Land'),
                'description' => 'Seorang musisi dan seorang aktris berjuang untuk mewujudkan impian mereka di Los Angeles.',
                'director' => 'Damien Chazelle',
                'writers' => 'Damien Chazelle',
                'stars' => 'Ryan Gosling, Emma Stone',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMzUzNDM2NzM2MV5BMl5BanBnXkFtZTgwNTM3NTg4OTE@._V1_SX300.jpg',
                'release_date' => '2016-12-09',
                'duration' => 128,
                'url_720' => 'https://example.com/la_la_land_720.mp4',
                'url_1080' => 'https://example.com/la_la_land_1080.mp4',
                'url_4k' => 'https://example.com/la_la_land_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Grand Budapest Hotel',
                'slug' => Str::slug('The Grand Budapest Hotel'),
                'description' => 'Petualangan seorang penjaga hotel legendaris yang terlibat dalam misteri pembunuhan.',
                'director' => 'Wes Anderson',
                'writers' => 'Wes Anderson, Hugo Guinness',
                'stars' => 'Ralph Fiennes, Tony Revolori',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMzM5NjUxOTEyMl5BMl5BanBnXkFtZTgwNjEyMDM0MDE@._V1_SX300.jpg',
                'release_date' => '2014-03-28',
                'duration' => 99,
                'url_720' => 'https://example.com/grand_budapest_720.mp4',
                'url_1080' => 'https://example.com/grand_budapest_1080.mp4',
                'url_4k' => 'https://example.com/grand_budapest_4k.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Masukkan data ke tabel movies
        DB::table('movies')->insert($movies);

        // Aktifkan kembali foreign key checks
        Schema::enableForeignKeyConstraints();
    }
}
