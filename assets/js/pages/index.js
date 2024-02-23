document.addEventListener('DOMContentLoaded', () => {
    let padding = { top: 40, right: 0, bottom: 0, left: 20 },
        width = 1000 - padding.left - padding.right,
        height = 1000 - padding.top - padding.bottom,
        radius = Math.min(width, height) / 2,
        oldRotation = 0,
        isSpinning = false,
        items = [],
        data = [],
        $winnerText = document.querySelector("#winnerText h1"),
        $chart = document.querySelector("#chart");

    const colors = { pieTextLight: "#fff", pieTextDark: "#000", pieBGDark: "rgba(0, 0, 0, 0.8)", pieBG: "rgba(0, 0, 0, 0.4)", wheelPieSelectorBG: "red", wheelSpinButtonBG: "white" }

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
                data: { itemId: itemId, itemQty: itemQty },
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
            .attr("id", (d, i) => {
                d.innerRadius = 0;
                d.outerRadius = radius;
                d.angle = (d.startAngle + d.endAngle) / 2;
                return `slice-${i + 1}`;
            })
            .attr("class", "slice");

        $arcs.append("defs")
            .append("pattern")
            .attr("id", function (d, i) { return `sliceImage-${i}`; })
            .attr("patternContentUnits", "objectBoundingBox")
            .attr("width", "100%")
            .attr("height", "100%")
            .append("image")
            .attr("xlink:href", function (d, i) { return items[i].itemImage ? `./uploads/${items[i].itemImage}` : "./assets/images/empty.jpg"; })
            .attr("preserveAspectRatio", "xMidYMid slice")
            .attr("width", "1")
            .attr("height", "1");

        $arcs.append("path")
            .attr("fill", function (d, i) { return `url(#sliceImage-${i})` })
            .attr("d", function (d) { return $arc(d); });

        $arcs.append("path")
            .attr("class", "sliceBG")
            .attr("fill", function (d, i) { return items[i].itemQty <= 0 ? colors.pieBGDark : colors.pieBG; })
            .attr("stroke", function (d, i) { return colors.pieTextDark; })
            .attr("d", function (d) { return $arc(d); });


        $arcs.append("text")
            .attr("fill", function (d, i) { return colors.pieTextLight; })
            .attr("transform", function (d, i) {
                return "rotate(" + (d.angle * 180 / Math.PI - 90) + ")translate(" + (d.outerRadius - 10) + ")";
            })
            .attr("text-anchor", "end")
            .text(function (d, i) {
                return data[i].label;
            });

        $svg.append("g")
            .attr("transform", `translate(${((width / 2) + padding.left)}, 0)`)
            .append("path")
            .attr("d", `M-${(radius * 0.05)}, 0L0, ${(radius * 0.15)}L${(radius * 0.05)}, 0Z`)
            .style({ "fill": colors.wheelPieSelectorBG });

        $container.append("circle")
            .attr("cx", 0)
            .attr("cy", 0)
            .attr("r", 60)
            .style({ "fill": colors.wheelSpinButtonBG, "cursor": "pointer" })
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
                let maxPieSliceDegree = 0;
                let minPieSliceDegree = pieSlice * -1;
                let rotation = ((Variable.rnd(6, 14) * 360) - (pieSlice * pickedIndex) + Variable.rnd((minPieSliceDegree + (pieSlice / 10)), (maxPieSliceDegree - (pieSlice / 10))));

                $vis.transition()
                    .duration(Variable.rnd(6, 12) * 1000)
                    .attrTween("transform", function () {
                        let i = d3.interpolate(oldRotation % 360, rotation);
                        return function (t) {
                            return "rotate(" + i(t) + ")";
                        };
                    })
                    .each("end", async function () {
                        if (pickedItemQty <= 0) {
                            d3.select(`#slice-${pickedIndex + 1} path.sliceBG`)
                                .attr("fill", colors.pieBGDark);

                            d3.select(`#slice-${pickedIndex + 1} text`)
                                .attr("fill", colors.pieTextLight);
                        }

                        if (winnerClass == "win") {
                            await updateItemQty(pickedItemId, pickedItemQty);
                            items[pickedIndex].itemQty = pickedItemQty;
                            toggleConfetti("🌟");
                            d3.select(`#slice-${pickedIndex + 1} text`)
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

    const getRandomColor = (opacity = "0.5") => {
        let color = 'rgba(';

        // Rastgele bir açık renk oluştur
        for (let i = 0; i < 3; i++) {
            color += Variable.rnd(20, 255); // 20-255 arası rastgele sayılar oluşturur
            if (i < 2) color += ', '; // Son bileşen hariç her bir bileşenin sonuna virgül ekler
        }

        // Alfa (şeffaflık) bileşeni ekler
        color += `, ${opacity})`; // Opacity değeri 0.6 olarak belirlenir

        return color;
    };

    getItems();
});