function drawChart(selector, data, keys, colors) {
    const container = document.querySelector(selector);
    if (!container) return;

    d3.select(selector).selectAll("*").remove();

    const margin = {top: 20, right: 30, bottom: 40, left: 50},
          width = container.offsetWidth - margin.left - margin.right,
          height = 250 - margin.top - margin.bottom;

    if (width <= 0) return;

    const svg = d3.select(selector)
        .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`);

    const parseTime = d3.timeParse("%Y-%m-%d %H:%M");
    const formattedData = data.map(d => ({
        ...d,
        date: typeof d.date === 'string' ? parseTime(d.date) : d.date
    })).filter(d => d.date !== null);

    if (formattedData.length === 0) return;

    // --- BRIDAGE DE L'AXE Y À 40 ---
    const realMax = d3.max(formattedData, d => Math.max(...keys.map(k => d[k] || 0)));
    const displayMax = Math.min(realMax, 40); 

    const x = d3.scaleTime()
        .domain(d3.extent(formattedData, d => d.date))
        .range([0, width]);

    const y = d3.scaleLinear()
        .domain([0, Math.max(displayMax * 1.1, 15)]) 
        .range([height, 0]);

    svg.append("g")
       .attr("transform", `translate(0,${height})`)
       .call(d3.axisBottom(x).ticks(5).tickFormat(d3.timeFormat("%d/%m")));

    svg.append("g").call(d3.axisLeft(y));

    keys.forEach((key, i) => {
        svg.append("path")
            .datum(formattedData)
            .attr("fill", "none")
            .attr("stroke", colors[i])
            .attr("stroke-width", 3)
            .attr("d", d3.line()
                .defined(d => d[key] !== null && d[key] <= 100)
                .x(d => x(d.date))
                .y(d => y(Math.min(d[key] || 0, 40)))
                .curve(d3.curveMonotoneX)
            );

        svg.selectAll(".dot-" + key)
            .data(formattedData.filter(d => d[key] !== null && d[key] <= 100))
            .enter().append("circle")
            .attr("cx", d => x(d.date))
            .attr("cy", d => y(Math.min(d[key] || 0, 40)))
            .attr("r", 4)
            .attr("fill", colors[i]);
    });
}

function initCharts() {
    if (typeof rawData !== 'undefined' && rawData.length > 0) {
        drawChart("#chart_gh", rawData, ["gh"], ["#7d7bc9"]);
        drawChart("#chart_ph_kh", rawData, ["ph", "kh"], ["#ff6f9c", "#4e73df"]);
        drawChart("#chart_toxic", rawData, ["nitrites", "ammonium"], ["#e74a3b", "#f6c23e"]);
    }
}

window.addEventListener('load', initCharts);
window.addEventListener('resize', initCharts);