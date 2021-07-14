/***

	This app is sponsored by business @ xparts.lv 2021
		- Code written by Naru Koshin
		- Github: https://github.com/narukoshin
		- Instagram: https://www.instagram.com/naru.koshin
***/

package main

import (
	"encoding/csv"
	"fmt"
	"log"
	"os"
	"strconv"
	"time"
)

func CsvReader(name string) [][]string{
	file, err := os.Open(name)
	if err != nil {
		log.Fatal(err)
	}
	defer file.Close()

	reader := csv.NewReader(file)
	reader.Comma = ';'
	reader.LazyQuotes = true

	data, err := reader.ReadAll()
	if err != nil {
		log.Fatal(err)
	}
	log.Printf("CSV: %s was loaded", name)

	return data

}

func main(){
	// creating logs
	logs, err := os.Create("runtime.log")
	if err != nil {
		log.Fatal(err)
	}
	defer logs.Close()
	log.SetOutput(logs)
	log.Println("Runtime log started")

	// starting count elapsed time
	start := time.Now()
	log.Println("Time counter started")

	// reading 3103900.csv
	data := CsvReader("3103900.csv")

	// reading STANY.csv
	data2 := CsvReader("STANY.csv")

	// reading INDEKS_PARAMETR.csv
	data3 := CsvReader("INDEKS_PARAMETR.csv")

	// reading 3103900_KAUCJE.csv
	data4 := CsvReader("3103900_KAUCJE.csv")

	// creating output file
	log.Println("CSV: compiled.csv was created")
	CREATE:
	var writerF *os.File
	if _, err := os.Stat("compiled.csv"); os.IsNotExist(err) {
		writerF, err = os.Create("compiled.csv")
		if err != nil {
			log.Fatal(err)
		}
	} else {
		os.Remove("compiled.csv")
		goto CREATE
	}
	defer writerF.Close()

	// creating new writer
	writer := csv.NewWriter(writerF)
	writer.Comma = ';'

	// main file - 3103900.csv
	log.Println("Starting combine all files into one")
	var i int
	for _, k := range data {
		i++
		fmt.Println(i)
		// second file - STANY.csv
		identifier := k[0]
		price, _ := strconv.ParseFloat(k[5], 64)
		code := k[6]
		currency := k[7]
		for _, v := range data2 {
			if identifier == v[0] {
				amount, _ := strconv.ParseFloat(v[1], 64)
				for _, h := range data4 {
					if identifier == h[0]{
						depositPrice, _ := strconv.ParseFloat(h[2], 64)
						price = price + depositPrice
					}
				}
				
				branch := v[2]
				// third file - INDEKS_PARAMETR.csv
				for _, p := range data3 {
					if identifier == p[0] {
						ret := p[1]
						price := fmt.Sprintf("%.2f", price)
						amount := strconv.FormatFloat(float64(amount), 'g', -1, 64)
						// price := strconv.FormatFloat(float64(price), 'g', -1, 64)
						tog := append(k[:5], price, code, currency, amount, branch, ret)
						writer.Write(tog)
						_ = tog
					}
				}
			}
		}
	}
	elapsed := time.Since(start)
	fmt.Println(elapsed)
	log.Printf("FINISHED: Elapsed time: %s\n", elapsed)
}