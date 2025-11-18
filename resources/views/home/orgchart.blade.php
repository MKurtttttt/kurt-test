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
    <div style="background-image: url('{{ asset('images/hau-main.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1; opacity: 0.3;"></div>
    <div class="max-w-7xl mx-auto px-4 py-12" style="position: relative; z-index: 1; min-height: calc(100vh - 256px);">
        <div id="orgchart" style="width: 100%; height: 810px; position: relative;"></div>
    </div>

    <!-- D3.js Script -->
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
        function createOrgChart() {
            // Clear any existing chart
            d3.select("#orgchart").selectAll("*").remove();

            const chartEl = document.getElementById('orgchart');
            const width = chartEl.offsetWidth;
            const containerHeight = chartEl.offsetHeight || window.innerHeight;

            // Calculate responsive scale factor using both width and height so text scales down on
            // narrow/narrow-and-short viewports (mobile). baseWidth/baseHeight are reference sizes.
            const baseWidth = 1200; // Reference width for scaling
            const baseHeight = 900; // Reference height for scaling
            const widthRatio = width / baseWidth;
            const heightRatio = containerHeight / baseHeight;

            // Use the smaller ratio to ensure content fits; allow a smaller minimum so text can shrink on tight screens.
            const scaleFactor = Math.max(0.25, Math.min(1.2, Math.min(widthRatio, heightRatio)));

            // Dynamic height based on scale factor - increased to accommodate all moved boxes
            const height = Math.max(500, 900 * scaleFactor);

            // Create SVG with viewBox for better scaling
            const svg = d3.select("#orgchart")
                .append("svg")
                .attr("width", width)
                .attr("height", height)
                .attr("viewBox", `0 0 ${width} ${height}`)
                .attr("preserveAspectRatio", "xMidYMid meet");

            // Define the organizational data with scaled positions
            // Shift all elements down by 200 pixels to create more vertical space
            const verticalShift = 200 * scaleFactor;
            
            const orgData = [
                // Top level - President
                {
                    id: "president",
                    name: "Mr. Leopoldo Jaime N. Valdes",
                    title: "President",
                    x: width / 2,
                    y: 80 * scaleFactor,
                    image: "{{ asset('images/profiles/president.jpg') }}"
                },
                // Second level - Director (moved down further)
                {
                    id: "director",
                    name: "Engr. Anne Marie A. Mangiliman",
                    title: "OIC - Director, Institutional Effectiveness",
                    x: width / 2,
                    y: 80 * scaleFactor + verticalShift,
                    parent: "president",
                    image: "{{ asset('images/profiles/director.jpg') }}"
                },
                // Third level - Department heads (moved down further)
                {
                    id: "qa",
                    name: "Dr. Alma Theresa D. Manaloto",
                    title: "Head, Quality Assurance",
                    x: width * 0.170,
                    y: 300 * scaleFactor + verticalShift,
                    parent: "director",
                    image: "{{ asset('images/profiles/qa.jpg') }}"
                },
                {
                    id: "planning",
                    name: "Engr. Anne Marie A. Mangiliman",
                    title: "Head, Institutional Planning, Research and Publications Office",
                    x: width * 0.40,
                    y: 300 * scaleFactor + verticalShift,
                    parent: "director",
                    image: "{{ asset('images/profiles/planning.jpg') }}"
                },
                {
                    id: "database",
                    name: "Engr. Maria Elena Y. Timbang",
                    title: "Head, Database Management Office",
                    x: width * 0.630,
                    y: 300 * scaleFactor + verticalShift,
                    parent: "director",
                    image: "{{ asset('images/profiles/database.jpg') }}"
                },
                {
                    id: "document",
                    name: "Ms. Corazon Q. Mallari",
                    title: "Institutional Document Controller",
                    x: width * 0.860,
                    y: 300 * scaleFactor + verticalShift,
                    parent: "director",
                    image: "{{ asset('images/profiles/document.jpg') }}"
                },
                // Fourth level - Staff (moved down further)
                {
                    id: "staff1",
                    name: "Ms. Bianca Ysabel L. Navarro",
                    title: "Coordinator, Institutional Planning, Research and Publications Office",
                    x: width * 0.40,
                    y: 500 * scaleFactor + verticalShift,
                    parent: "planning",
                    image: "{{ asset('images/profiles/staff1.jpg') }}"
                },
                {
                    id: "staff2",
                    name: "Ms. Angela Joy D. Villar",
                    title: "Staff, Institutional Document Controller",
                    x: width * 0.860,
                    y: 500 * scaleFactor + verticalShift,
                    parent: "document",
                    image: "{{ asset('images/profiles/staff2.jpg') }}"
                }
            ];

            // Create connections with scaled dimensions (adjusted for vertical shift)
            const midY1 = 200 * scaleFactor + verticalShift; // First horizontal line for QA, Planning, Database
            const midY2 = 175 * scaleFactor + verticalShift; // Second horizontal line for IDC
            const lineWidth = Math.max(2, 3 * scaleFactor); // Scaled line width
            
            // Vertical line from president to director
            svg.append("path")
                .attr("class", "connection president-to-director")
                .attr("d", `M ${width / 2} ${50 * scaleFactor + 60 * scaleFactor} L ${width / 2} ${80 * scaleFactor + verticalShift - 60 * scaleFactor}`)
                .attr("stroke", "#5c542c")
                .attr("stroke-width", lineWidth)
                .attr("fill", "none");
            
            // Main vertical line from director (adjusted for vertical shift)
            svg.append("path")
                .attr("class", "connection main-vertical")
                .attr("d", `M ${width / 2} ${80 * scaleFactor + 60 * scaleFactor + verticalShift} L ${width / 2} ${midY2 + 25 * scaleFactor}`)
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
            // Vertical lines down to all 4 department squares (centered, adjusted for vertical shift)
            const deptCenters = [
                { x: width * 0.17, y: midY1 },      // QA
                { x: width * 0.40, y: midY1 },      // Planning
                { x: width * 0.63, y: midY1 },      // Database
                { x: width * 0.86, y: midY2 }       // Document Controller (uses midY2)
            ];

            deptCenters.forEach((dept, i) => {
                // QA, Planning, Database use midY1, Document uses midY2
                const targetY = (300 - 60) * scaleFactor + verticalShift;
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
            // Font sizes scale with the scaleFactor. Minimums lowered to allow smaller screens to shrink text
            // while still remaining legible.
            // Font sizes scale with the scaleFactor. Minimums lowered so labels can shrink on small screens.
            const fontSize = {
                name: Math.max(9, 14 * scaleFactor),
                title: Math.max(7, 12 * scaleFactor),
                initials: Math.max(10, 18 * scaleFactor)
            };

            // Add main rectangles
            nodes.append("rect")
                .attr("x", -boxWidth/2)
                .attr("y", -boxHeight/2)
                .attr("width", boxWidth)
                .attr("height", boxHeight)
                .attr("rx", 8 * scaleFactor)
                .attr("fill", d => d.id === "president" ? "#8B1538" : "#a39372") // Lighter maroon for president, gold for others
                .attr("stroke", d => d.id === "president" ? "#70121D" : "#5c542c") // Dark maroon outline for president, brown for others
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
                .attr("stroke", d => d.id === "president" ? "#70121D" : "#5c542c") // Dark maroon stroke for president, brown for others
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
                .attr("fill", d => d.id === "president" ? "#8B1538" : "#a39372") // Light maroon for president, brown for others
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
                .attr("fill", "white") // White text for all positions for visibility
                .each(function(d) {
                    const text = d3.select(this);
                    const words = d.name.split(' ');
                    text.text('');
                    
                    // Special case for president - display name in single line
                    if (d.id === "president") {
                        text.append('tspan')
                            .attr('x', 0)
                            .attr('dy', 0)
                            .text(d.name);
                    } else {
                        // Regular text wrapping for other nodes
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
                    }
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
                        .attr("fill", d.id === "president" ? "#70121D" : "#5c542c"); // Dark maroon hover for president, brown for others
                })
                .on("mouseout", function(event, d) {
                    d3.select(this).select("rect")
                        .transition()
                        .duration(200)
                        .attr("fill", d.id === "president" ? "#8B1538" : "#a39372"); // Restore to light maroon for president, original for others
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
        /* Improve SVG text rendering */
        #orgchart svg text {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            shape-rendering: geometricPrecision;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        @media (max-width: 420px) {
            #orgchart { height: 520px !important; }
        }
    </style>
@endsection
