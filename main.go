/***

	This app is sponsored by business @ xparts.lv 2021
		- Code written by Naru Koshin
		- Github: https://github.com/narukoshin
		- Instagram: https://www.instagram.com/naru.koshin
***/

package main

import (
	"encoding/csv"
	"errors"
	"fmt"
	"log"
	"os"
	"strconv"
	"time"
)

const L_DELETE 			= "delete"
const L_CREATE 			= "create"
const L_FNAME  			= ".naruconv_lock"
var	  ErrFileExists		= errors.New(fmt.Sprintf("error: the process is already running, if it's not, then please delete %s file.", L_FNAME))

func CsvReader(name string) (data [][]string, err error){
	var file *os.File
	file, err = os.Open(name)
	if err != nil {
		return nil, err
	}
	defer file.Close()

	reader := csv.NewReader(file)
	reader.Comma = ';'
	reader.LazyQuotes = true

	data, err = reader.ReadAll()
	if err != nil {
		return nil, err
	}
	log.Printf("CSV: %s was loaded", name)

	return data, nil

}

// this function creates a locker that tells that script is still working and the result file is not ready
func Locker(action string) (err error){
	if action == L_DELETE {
		os.Remove(L_FNAME)
	} else if action == L_CREATE {
		if _, err := os.Stat(L_FNAME); !os.IsNotExist(err) {
			return ErrFileExists
		}
		os.Create(L_FNAME)
	}
	return
}

func main(){
	// creating a locker
	err := Locker(L_CREATE)
	if err != nil {
		// writing to runtime.log file that we failed to run a script
		log.Fatal(err)
	}
	defer Locker(L_DELETE)
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
	data, err := CsvReader("3103900.csv")
	if err != nil {
		Locker(L_DELETE)
		log.Fatal(err)
	}

	// reading STANY.csv
	data2, err := CsvReader("STANY.csv")
	if err != nil {
		Locker(L_DELETE)
		log.Fatal(err)
	}

	// reading INDEKS_PARAMETR.csv
	data3, err := CsvReader("INDEKS_PARAMETR.csv")
	if err != nil {
		Locker(L_DELETE)
		log.Fatal(err)
	}

	// reading 3103900_KAUCJE.csv
	data4, err := CsvReader("3103900_KAUCJE.csv")
	if err != nil {
		Locker(L_DELETE)
		log.Fatal(err)
	}

	// creating output file
	log.Println("CSV: compiled.csv was created")
	CREATE:
	var writerF *os.File
	if _, err := os.Stat("compiled.csv"); os.IsNotExist(err) {
		writerF, err = os.Create("compiled.csv")
		if err != nil {
			Locker(L_DELETE)
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
	for _, k := range data {
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