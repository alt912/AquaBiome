// Fonction générique pour créer un graphique linéaire
function drawChart(selector, data, keys, colors) {
    const margin = {top: 20, right: 30, bottom: 40, left: 50},
          width = document.querySelector(selector).offsetWidth - margin.left - margin.right,
          height = 250 - margin.top - margin.bottom;

    const svg = d3.select(selector)
        .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`);

    // Formatage des dates
    const parseTime = d3.timeParse("%Y-%m-%d %H:%M");
    const formattedData = data.map(d => ({
        ...d,
        date: parseTime(d.date)
    }));

    // Échelles
    const x = d3.scaleTime()
        .domain(d3.extent(formattedData, d => d.date))
        .range([0, width]);

    const y = d3.scaleLinear()
        .domain([0, d3.max(formattedData, d => Math.max(...keys.map(k => d[k]))) * 1.2])
        .range([height, 0]);

    // Axes
    svg.append("g")
       .attr("transform", `translate(0,${height})`)
       .call(d3.axisBottom(x).ticks(5));
    svg.append("g").call(d3.axisLeft(y));

    // Dessin des lignes
    keys.forEach((key, i) => {
        svg.append("path")
            .datum(formattedData)
            .attr("fill", "none")
            .attr("stroke", colors[i])
            .attr("stroke-width", 3)
            .attr("d", d3.line()
                .x(d => x(d.date))
                .y(d => y(d[key]))
                .curve(d3.curveMonotoneX)
            );
    });
}

// Lancement des graphiques si on a des données
if (rawData.length > 0) {
    drawChart("#chart_gh", rawData, ["gh"], ["#7d7bc9"]);
    drawChart("#chart_ph_kh", rawData, ["ph", "kh"], ["#ff6f9c", "#4e73df"]);
    drawChart("#chart_toxic", rawData, ["nitrites", "ammonium"], ["#e74a3b", "#f6c23e"]);
}