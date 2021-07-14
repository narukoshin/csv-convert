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

func main(){
	// creating logs
	logs, err := os.Create("runtime.log")
	if err != nil {
		log.Fatal(err)
	}
	defer logs.Close()
	log.SetOutput(logs)

	start := time.Now()
	// reading the file
	file, err := os.Open("3103900.csv")
	if err != nil {
		log.Fatal(err)
	}
	defer file.Close()

	// starting reading the csv file
	reader := csv.NewReader(file)
	reader.Comma = ';'
	reader.LazyQuotes = true

	// converting csv to array
	data, err := reader.ReadAll()
	if err != nil {
		log.Fatal(err)
	}

	// reading second file

	file2, err := os.Open("STANY.csv")
	if err != nil {
		log.Fatal(err)
	}

	// reading second file csv
	reader2 := csv.NewReader(file2)
	reader2.Comma = ';'
	reader2.LazyQuotes = true

	data2, err := reader2.ReadAll()
	if err != nil {
		log.Fatal(err)
	}

	// opening third csv file
	file3, err := os.Open("INDEKS_PARAMETR.csv")
	if err != nil {
		log.Fatal(err)
	}
	defer file3.Close()

	// reading csv from third file
	reader3 := csv.NewReader(file3)
	reader3.Comma = ';'
	reader3.LazyQuotes = true

	data3, err := reader3.ReadAll()
	if err != nil {
		log.Fatal(err)
	}

	// opening fourth csv file
	file4, err := os.Open("3103900_KAUCJE.csv")
	if err != nil {
		log.Fatal(err)
	}
	defer file4.Close()

	// reading csv from fourth file
	reader4 := csv.NewReader(file4)
	reader4.Comma = ';'
	reader4.LazyQuotes = true

	data4, err := reader4.ReadAll()
	if err != nil {
		log.Fatal(err)
	}

	
	// creating output file
	CREATE:
	var writerF *os.File
	if _, err := os.Stat("result.csv"); os.IsNotExist(err) {
		writerF, err = os.Create("result.csv")
		if err != nil {
			log.Fatal(err)
		}
	} else {
		os.Remove("result.csv")
		goto CREATE
	}
	defer writerF.Close()

	// creating new writer
	writer := csv.NewWriter(writerF)
	writer.Comma = ';'

	// main file - 3103900.csv
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
}