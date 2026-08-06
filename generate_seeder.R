library(anthro)

# Indices to process
indices <- list(
  waz = anthro:::growthstandards_weianthro,
  haz = anthro:::growthstandards_lenanthro,
  bmiz = anthro:::growthstandards_bmianthro,
  hcfa = anthro:::growthstandards_hcanthro
)

# Open file
sink("database/seeders/FullWhoReferenceSeeder.php")

cat("<?php\n\nnamespace Database\\Seeders;\n\nuse Illuminate\\Database\\Seeder;\nuse Illuminate\\Support\\Facades\\DB;\n\n")
cat("class FullWhoReferenceSeeder extends Seeder\n{\n    public function run()\n    {\n")
cat("        DB::table('who_growth_references')->truncate();\n")
cat("        $data = [\n")

for (indeks in names(indices)) {
  df <- indices[[indeks]]
  # Process sex: 1=L, 2=P
  for (s in c(1, 2)) {
    sub_df <- df[df$sex == s, ]
    # Loop over months 0 to 60
    for (m in 0:60) {
      target_day <- round(m * 30.4375)
      # Find closest day
      row <- sub_df[which.min(abs(sub_df$age - target_day)), ]
      
      jenis_kelamin <- ifelse(s == 1, "L", "P")
      L_val <- round(row$l, 4)
      M_val <- round(row$m, 4)
      S_val <- round(row$s, 4)
      
      cat(sprintf("            ['indeks' => '%s', 'jenis_kelamin' => '%s', 'usia_bulan' => %d, 'L' => %s, 'M' => %s, 'S' => %s],\n",
                  indeks, jenis_kelamin, m, L_val, M_val, S_val))
    }
  }
}

cat("        ];\n\n")
cat("        foreach (array_chunk($data, 100) as $chunk) {\n")
cat("            DB::table('who_growth_references')->insert($chunk);\n")
cat("        }\n")
cat("    }\n}\n")

sink()
