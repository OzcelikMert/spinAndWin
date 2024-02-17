document.addEventListener('DOMContentLoaded', () => {
    let padding = { top: 20, right: 40, bottom: 0, left: 0 },
        width = 1000 - padding.left - padding.right,
        height = 1000 - padding.top - padding.bottom,
        radius = Math.min(width, height) / 2,
        oldRotation = 0,
        isSpinning = false,
        items = [],
        data = [],
        $winnerText = document.querySelector("#winnerText h1"),
        $chart = document.querySelector("#chart");

    function getItems() {
        $.Toast.showToast({
            "title": "Yükleniyor...",
            "icon": "loading",
            "duration": 0
        });

        $.ajax({
            url: "api/get.php",
            type: "GET",
            data: { query: "OK" },
            success: (res) => {
                let json = JSON.parse(res);
                if (json.status && Array.isArray(json.rows)) {
                    items = json.rows;
                    data = items.map(function (item) {
                        return {
                            label: `${item.itemText} (${item.itemQty} Adet)`,
                            value: item.itemId
                        };
                    });
                }
                initWheel();
                $.Toast.hideToast();
            },
            error: () => {
                $.Toast.hideToast();
                Swal.fire({
                    icon: 'error',
                    title: "Server Error!",
                    text: 'Please you should contact to admin.'
                })
            }
        })
    }

    async function updateItemQty(itemId, itemQty) {
        $.Toast.showToast({
            "title": "Güncelleniyor...",
            "icon": "loading",
            "duration": 0
        });

        await new Promise(resolve => {
            $.ajax({
                url: "api/updateQty.php",
                type: "POST",
                data: { itemId: itemId, itemQty: itemQty},
                success: (res) => {
                    resolve(1);
                },
                error: () => {
                    Swal.fire({
                        icon: 'error',
                        title: "Server Error!",
                        text: 'Please you should contact to admin.'
                    })
                    resolve(1);
                }
            })
        })

        $.Toast.hideToast();
    }

    function initWheel() {
        let $svg = d3.select('#chart')
            .append("svg")
            .data([data])
            .attr("width", width + padding.left + padding.right)
            .attr("height", height + padding.top + padding.bottom);

        let $container = $svg.append("g")
            .attr("class", "chart-holder")
            .attr("transform", "translate(" + (width / 2 + padding.left) + "," + (height / 2 + padding.top) + ")");

        let $vis = $container.append("g");

        let $pie = d3.layout.pie().sort(null).value(function (d) { return 1; });

        let $arc = d3.svg.arc().outerRadius(radius);

        let $arcs = $vis.selectAll("g.slice")
            .data($pie)
            .enter()
            .append("g")
            .attr("class", "slice");

        $arcs.append("path")
            .attr("fill", function (d, i) { return items[i].itemQty <= 0 ? "#444" : getRandomColor(); })
            .attr("d", function (d) { return $arc(d); });

        $arcs.append("text")
            .attr("fill", function (d, i) { return items[i].itemQty <= 0 ? "#fff" : "#000"; })
            .attr("transform", function (d) {
                d.innerRadius = 0;
                d.outerRadius = radius;
                d.angle = (d.startAngle + d.endAngle) / 2;
                return "rotate(" + (d.angle * 180 / Math.PI - 90) + ")translate(" + (d.outerRadius - 10) + ")";
            })
            .attr("text-anchor", "end")
            .text(function (d, i) {
                return data[i].label;
            });


        $svg.append("g")
            .attr("transform", "translate(" + (width + padding.left + padding.right) + "," + ((height / 2) + padding.top) + ")")
            .append("path")
            .attr("d", "M-" + (radius * .15) + ",0L0," + (radius * .05) + "L0,-" + (radius * .05) + "Z")
            .style({ "fill": "red" });

        $container.append("circle")
            .attr("cx", 0)
            .attr("cy", 0)
            .attr("r", 60)
            .style({ "fill": "white", "cursor": "pointer" })
            .on("click", spin);

        $container.append("text")
            .attr("x", 0)
            .attr("y", 10)
            .attr("text-anchor", "middle")
            .text("Döndür")
            .style({ "font-weight": "bold", "font-size": "30px" });

        async function spin(d) {
            if (!isSpinning) {
                if (items.every(item => item.itemQty <= 0)) {
                    return;
                }

                isSpinning = true;
                let winnerMessage = "Çarkı çevirmek için 'Döndür' e basın.";
                let winnerClass = "lose";

                let totalProbability = items.reduce((acc, item) => acc + item.itemProbability, 0);
                let randomNumber = Math.random() * totalProbability;
                let cumulativeProbability = 0;
                let pickedItemId = "";
                let pickedItemQty = 0;
                let pickedIndex = -1;

                for (let i = 0; i < items.length; i++) {
                    cumulativeProbability += items[i].itemProbability;
                    if (randomNumber <= cumulativeProbability) {
                        pickedItemId = items[i].itemId;
                        pickedItemQty = items[i].itemQty;
                        pickedIndex = i;
                        break;
                    }
                }

                if (pickedItemQty <= 0) {
                    winnerMessage = "Üzgünüm şans bu sefer senden yana değil 😥. Tekrar dene!"
                } else {
                    pickedItemQty = pickedItemQty - 1;
                    winnerMessage = `'${items[pickedIndex].itemText}' kazandın, Tebrikler! 🤩🎉`;
                    winnerClass = "win";
                }

                let pieSlice = 360 / items.length;
                let rotation = (360 - (pieSlice * pickedIndex) + Variable.rnd(35, 85)) + (Variable.rnd(6, 14) * 360);

                $vis.transition()
                    .duration(8000)
                    .attrTween("transform", function () {
                        let i = d3.interpolate(oldRotation % 360, rotation);
                        return function (t) {
                            return "rotate(" + i(t) + ")";
                        };
                    })
                    .each("end", async function () {
                        if(pickedItemQty <= 0){
                            d3.select(`.slice:nth-child(${(pickedIndex + 1)}) path`)
                            .attr("fill", "#444");

                            d3.select(`.slice:nth-child(${(pickedIndex + 1)}) text`)
                                .attr("fill", "#fff");
                        }

                        if(winnerClass == "win"){
                            await updateItemQty(pickedItemId, pickedItemQty);
                            items[pickedIndex].itemQty = pickedItemQty;
                            toggleConfetti("🌟");
                            d3.select(`.slice:nth-child(${(pickedIndex + 1)}) text`)
                                .text(`${items[pickedIndex].itemText} (${items[pickedIndex].itemQty} Adet)`);;
                        }
                        
                        $winnerText.classList.remove("win", "lose");
                        $winnerText.classList.add(winnerClass)
                        $winnerText.innerHTML = winnerMessage;
                        oldRotation = rotation;
                        isSpinning = false;

                        if (items.every(item => item.itemQty <= 0)) {
                            $winnerText.innerHTML = `${winnerMessage} <br/> Tüm çekilişler yapılmıştır 🎉. Kazanan herkesi tebrik ederim!`;
                            setInterval(() => toggleConfetti("🎉"), 3000);
                            return;
                        }
                    });
            }
        }
    }

    const getRandomColor = () => {
        const letters = 'ABCDEF';
        let color = '#';
    
        // Rastgele bir açık renk oluştur
        for (let i = 0; i < 3; i++) {
            color += letters[Math.floor(Math.random() * 6)];
        }
    
        // Yazı rengi için kontrast kontrolü
        // R: 16, G: 16, B: 16'nın altına inmemeli
        if (parseInt(color.substring(1, 3), 16) < 16 &&
            parseInt(color.substring(3, 5), 16) < 16 &&
            parseInt(color.substring(5, 7), 16) < 16) {
            // Renkleri artır
            color = '#' + (
                parseInt(color.substring(1, 3), 16) + 32
            ).toString(16).padStart(2, '0') + (
                parseInt(color.substring(3, 5), 16) + 32
            ).toString(16).padStart(2, '0') + (
                parseInt(color.substring(5, 7), 16) + 32
            ).toString(16).padStart(2, '0');
        }
    
        return color;
    };

    getItems();
});