@extends('layouts.main')

@section('title', 'Organizational Chart')

@section('content')
    <!-- Image Banner Title -->
    <div class="relative" style="background-image: url('{{ asset('images/hau-bg.png') }}'); background-size: cover; background-position: center; height: 16rem; min-height: 256px; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.4); display: flex; align-items: center; justify-content: center;">
            <div style="text-align: center; color: white;">
                <h1 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem; color: white;">
                    Office of Institutional Effectiveness
                </h1>
                <p style="font-size: 1.25rem; opacity: 0.9; color: white;">
                    Organizational Chart
                </p>
            </div>
        </div>
    </div>

    <!-- Organizational Chart -->
    <div style="background-image: url('{{ asset('images/bg-white.png') }}'); background-size: cover; background-position: center; background-attachment: fixed; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1;"></div>
    <div class="max-w-7xl mx-auto px-4 py-12" style="position: relative; z-index: 1; min-height: calc(100vh - 256px);">
        <div id="orgchart" style="width: 100%; height: 600px; position: relative;"></div>
    </div>

    <!-- D3.js Script -->
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
        function createOrgChart() {
            // Clear any existing chart
            d3.select("#orgchart").selectAll("*").remove();

            const width = document.getElementById('orgchart').offsetWidth;
            
            // Calculate responsive scale factor
            const baseWidth = 1200; // Reference width for scaling
            const scaleFactor = Math.max(0.5, Math.min(1.2, width / baseWidth));
            
            // Dynamic height based on scale factor
            const height = Math.max(400, 600 * scaleFactor);

            // Create SVG with viewBox for better scaling
            const svg = d3.select("#orgchart")
                .append("svg")
                .attr("width", width)
                .attr("height", height)
                .attr("viewBox", `0 0 ${width} ${height}`)
                .attr("preserveAspectRatio", "xMidYMid meet");

            // Define the organizational data with scaled positions
            const orgData = [
                // Top level - Director
                {
                    id: "director",
                    name: "Engr. Anne Marie A. Mangiliman",
                    title: "OIC - Director, Institutional Effectiveness",
                    x: width / 2,
                    y: 80 * scaleFactor,
                    image: "{{ asset('images/profiles/director.jpg') }}"
                },
                // Second level - Department heads
                {
                    id: "qa",
                    name: "Dr. Alma Theresa D. Manaloto",
                    title: "Head, Quality Assurance",
                    x: width * 0.170,
                    y: 300 * scaleFactor,
                    parent: "director",
                    image: "{{ asset('images/profiles/qa.jpg') }}"
                },
                {
                    id: "planning",
                    name: "Engr. Anne Marie A. Mangiliman",
                    title: "Head, Institutional Planning, Research and Publications Office",
                    x: width * 0.40,
                    y: 300 * scaleFactor,
                    parent: "director",
                    image: "{{ asset('images/profiles/planning.jpg') }}"
                },
                {
                    id: "database",
                    name: "Engr. Maria Elena Y. Timbang",
                    title: "Head, Database Management Office",
                    x: width * 0.630,
                    y: 300 * scaleFactor,
                    parent: "director",
                    image: "{{ asset('images/profiles/database.jpg') }}"
                },
                {
                    id: "document",
                    name: "Ms. Corazon Q. Mallari",
                    title: "Institutional Document Controller",
                    x: width * 0.860,
                    y: 300 * scaleFactor,
                    parent: "director",
                    image: "{{ asset('images/profiles/document.jpg') }}"
                },
                // Third level - Staff
                {
                    id: "staff1",
                    name: "Ms. Bianca Ysabel L. Navarro",
                    title: "Staff, Institutional Planning, Research and Publications Office",
                    x: width * 0.40,
                    y: 500 * scaleFactor,
                    parent: "planning",
                    image: "{{ asset('images/profiles/staff1.jpg') }}"
                },
                {
                    id: "staff2",
                    name: "Ms. Angela Joy D. Villar",
                    title: "Staff, Institutional Document Controller",
                    x: width * 0.860,
                    y: 500 * scaleFactor,
                    parent: "document",
                    image: "{{ asset('images/profiles/staff2.jpg') }}"
                }
            ];

            // Create connections with scaled dimensions
            const midY1 = 200 * scaleFactor; // First horizontal line for QA, Planning, Database
            const midY2 = 175 * scaleFactor; // Second horizontal line for IDC
            const lineWidth = Math.max(2, 3 * scaleFactor); // Scaled line width
            
            // Main vertical line from director
            svg.append("path")
                .attr("class", "connection main-vertical")
                .attr("d", `M ${width / 2} ${80 * scaleFactor + 60 * scaleFactor} L ${width / 2} ${midY2 + 25 * scaleFactor}`)
                .attr("stroke", "#5c542c")
                .attr("stroke-width", lineWidth)
                .attr("fill", "none");
            
            // First horizontal line for QA, Planning, Database (3 departments)
            svg.append("path")
                .attr("class", "connection horizontal-line-1")
                .attr("d", `M ${width / 2} ${midY1} L ${width * 0.631} ${midY1}`) // From center to Database position
                .attr("stroke", "#5c542c")
                .attr("stroke-width", lineWidth)
                .attr("fill", "none");
                
            // Extend first horizontal line to the left for QA and Planning
            svg.append("path")
                .attr("class", "connection horizontal-line-1-left")
                .attr("d", `M ${width / 2} ${midY1} L ${width * 0.169} ${midY1}`) // From center to QA position
                .attr("stroke", "#5c542c")
                .attr("stroke-width", lineWidth)
                .attr("fill", "none");
            
            // Second horizontal line for IDC
            svg.append("path")
                .attr("class", "connection horizontal-line-2")
                .attr("d", `M ${width / 2} ${midY2} L ${width * 0.861} ${midY2}`) // From center to IDC position
                .attr("stroke", "#5c542c")
                .attr("stroke-width", lineWidth)
                .attr("fill", "none");
            
            // Vertical lines down to first 3 departments
            // Vertical lines down to all 4 department squares (centered)
            const deptCenters = [
                { x: width * 0.17, y: midY1 },      // QA
                { x: width * 0.40, y: midY1 },      // Planning
                { x: width * 0.63, y: midY1 },      // Database
                { x: width * 0.86, y: midY2 }       // Document Controller (uses midY2)
            ];

            deptCenters.forEach((dept, i) => {
                // QA, Planning, Database use midY1, Document uses midY2
                const targetY = (300 - 60) * scaleFactor;
                svg.append("path")
                    .attr("class", "connection vertical-to-dept")
                    .attr("d", `M ${dept.x} ${dept.y} L ${dept.x} ${targetY}`)
                    .attr("stroke", "#5c542c")
                    .attr("stroke-width", lineWidth)
                    .attr("fill", "none");
            });
            
            // Draw connections from departments to their staff
            const staffConnections = [
                { from: orgData.find(n => n.id === "planning"), to: orgData.find(n => n.id === "staff1") },
                { from: orgData.find(n => n.id === "document"), to: orgData.find(n => n.id === "staff2") }
            ];
            
            staffConnections.forEach(conn => {
                const midStaffY = conn.from.y + (conn.to.y - conn.from.y) / 2;
                svg.append("path")
                    .attr("class", "connection staff-connection")
                    .attr("d", `M ${conn.from.x} ${conn.from.y + 60 * scaleFactor} L ${conn.from.x} ${midStaffY} L ${conn.to.x} ${midStaffY} L ${conn.to.x} ${conn.to.y - 60 * scaleFactor}`)
                    .attr("stroke", "#5c542c")
                    .attr("stroke-width", lineWidth)
                    .attr("fill", "none");
            });

            // Create node groups
            const nodes = svg.selectAll(".node")
                .data(orgData)
                .enter()
                .append("g")
                .attr("class", "node")
                .attr("transform", d => `translate(${d.x}, ${d.y})`);

            // Scaled sizes for responsive design
            const boxWidth = 250 * scaleFactor;
            const boxHeight = 150 * scaleFactor;
            const circleRadius = 30 * scaleFactor;
            const fontSize = {
                name: Math.max(10, 12 * scaleFactor),
                title: Math.max(8, 10 * scaleFactor),
                initials: Math.max(14, 18 * scaleFactor)
            };

            // Add main rectangles
            nodes.append("rect")
                .attr("x", -boxWidth/2)
                .attr("y", -boxHeight/2)
                .attr("width", boxWidth)
                .attr("height", boxHeight)
                .attr("rx", 8 * scaleFactor)
                .attr("fill", "#a39372")
                .attr("stroke", "#5c542c")
                .attr("stroke-width", lineWidth);

            // Add profile circles with clipping path for images

            nodes.append("defs")
                .append("clipPath")
                .attr("id", d => `clip-${d.id}`)
                .append("circle")
                .attr("cx", 0)
                .attr("cy", -25 * scaleFactor)
                .attr("r", circleRadius);

            // Add profile circle backgrounds
            nodes.append("circle")
                .attr("cx", 0)
                .attr("cy", -25 * scaleFactor)
                .attr("r", circleRadius)
                .attr("fill", "white")
                .attr("stroke", "#5c542c")
                .attr("stroke-width", lineWidth);

            // Add profile images
            nodes.append("image")
                .attr("x", -circleRadius)
                .attr("y", -55 * scaleFactor)
                .attr("width", circleRadius * 2)
                .attr("height", circleRadius * 2)
                .attr("clip-path", d => `url(#clip-${d.id})`)
                .attr("href", d => d.image)
                .on("error", function(event, d) {
                    // If image fails to load, hide it and show initials instead
                    d3.select(this).style("display", "none");
                    d3.select(this.parentNode).select(".initials-text").style("display", "block");
                });

            // Add profile initials as fallback (initially hidden)
            nodes.append("text")
                .attr("class", "initials-text")
                .attr("x", 0)
                .attr("y", -18.6 * scaleFactor)
                .attr("text-anchor", "middle")
                .attr("font-family", "Inter, sans-serif")
                .attr("font-size", `${fontSize.initials}px`)
                .attr("font-weight", "bold")
                .attr("fill", "#a39372")
                .style("display", "none") // Hidden by default, shown if image fails
                .text(d => {
                    const names = d.name.split(' ');
                    // Use second name if available, otherwise first
                    const second = names.length > 1 ? names[1] : names[0];
                    const last = names[names.length - 1];
                    return second.charAt(0) + (last ? last.charAt(0) : '');
                });

            // Add names
            nodes.append("text")
                .attr("x", 0)
                .attr("y", 25 * scaleFactor)
                .attr("text-anchor", "middle")
                .attr("font-family", "Inter, sans-serif")
                .attr("font-size", `${fontSize.name}px`)
                .attr("font-weight", "bold")
                .attr("fill", "white")
                .each(function(d) {
                    const text = d3.select(this);
                    const words = d.name.split(' ');
                    text.text('');
                    
                    let line = [];
                    let lineNumber = 0;
                    
                    words.forEach(word => {
                        line.push(word);
                        if (line.join(' ').length > 20 || words.indexOf(word) === words.length - 1) {
                            text.append('tspan')
                                .attr('x', 0)
                                .attr('dy', lineNumber === 0 ? 0 : `${1.1 * scaleFactor}em`)
                                .text(line.join(' '));
                            line = [];
                            lineNumber++;
                        }
                    });
                });

            // Add titles
            nodes.append("text")
                .attr("x", 0)
                .attr("y", 40 * scaleFactor)
                .attr("text-anchor", "middle")
                .attr("font-family", "Inter, sans-serif")
                .attr("font-size", `${fontSize.title}px`)
                .attr("fill", "rgba(255,255,255,0.9)")
                .each(function(d) {
                    const text = d3.select(this);
                    const words = d.title.split(' ');
                    text.text('');
                    
                    let line = [];
                    let lineNumber = 0;
                    
                    words.forEach(word => {
                        line.push(word);
                        if (line.join(' ').length > 25 || words.indexOf(word) === words.length - 1) {
                            text.append('tspan')
                                .attr('x', 0)
                                .attr('dy', lineNumber === 0 ? 0 : `${1.1 * scaleFactor}em`)
                                .text(line.join(' '));
                            line = [];
                            lineNumber++;
                        }
                    });
                });

            // Add hover effects
            nodes
                .on("mouseover", function(event, d) {
                    d3.select(this).select("rect")
                        .transition()
                        .duration(200)
                        .attr("fill", "#5c542c");
                })
                .on("mouseout", function(event, d) {
                    d3.select(this).select("rect")
                        .transition()
                        .duration(200)
                        .attr("fill", "#a39372");
                });
        }

        // Initialize when page loads
        window.addEventListener('DOMContentLoaded', createOrgChart);
        
        // Recreate chart on window resize
        window.addEventListener('resize', function() {
            setTimeout(createOrgChart, 100);
        });
    </script>

    <!-- Custom Styling -->
    <style>
        #orgchart {
            /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); */
            overflow: hidden;
        }
        
        #orgchart svg {
            display: block;
            margin: 0 auto;
        }
        
        .node {
            cursor: pointer;
        }
        
        .connection {
            pointer-events: none;
        }
        
        .node text {
            pointer-events: none;
            user-select: none;
        }
    </style>
@endsection