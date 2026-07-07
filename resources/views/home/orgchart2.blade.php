@extends('layouts.home')

@section('title', 'Organizational Chart')

@section('content')

    {{-- Page Header --}}
    <div class="flex flex-col items-center justify-center pt-10 md:pt-14 px-4 relative z-10 w-full mb-10">
        <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] mb-4 px-4 py-1.5 rounded-full" style="background: rgba(197,160,89,0.22); color: #f0d080; border: 1px solid rgba(197,160,89,0.35);">
            <i class="fas fa-sitemap text-xs"></i>
            OIE &mdash; Structure
        </div>
        <h1 class="page-hero-title text-3xl md:text-4xl lg:text-5xl text-center mb-3">Organizational Chart</h1>
        <p class="page-hero-sub text-base md:text-lg font-light text-center max-w-xl">
            Office of Institutional Effectiveness
        </p>
    </div>

    {{-- Chart Container --}}
    <div class="oie-chart-wrap">
        <div id="orgchart"></div>
    </div>

    @push('scripts')
    <script src="https://d3js.org/d3.v7.min.js" defer crossorigin="anonymous"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
    (function () {

        //Data
        const DATA = {
            id: "president",
            name: "Mr. Leopoldo Jaime N. Valdes",
            title: "University President",
            image: "{{ asset('images/profiles/president.jpg') }}",
            children: [
                {
                    id: "director",
                    name: "Engr. Anne Marie A. Mangiliman",
                    title: "OIC-Director | Chief Quality Institutional Effectiveness",
                    image: "{{ asset('images/profiles/director.png') }}",
                    children: [
                        {
                            id: "qa",
                            name: "Dr. Alma Theresa D. Manaloto",
                            title: "Head, Quality Assurance",
                            image: "{{ asset('images/profiles/qa.png') }}"
                        },
                        {
                            id: "planning",
                            name: "Engr. Anne Marie A. Mangiliman",
                            title: "Head, Institutional Planning, Research & Publications",
                            image: "{{ asset('images/profiles/planning.png') }}",
                            children: [
                                {
                                    id: "staff1",
                                    name: "Ms. Bianca Ysabel L. Navarro",
                                    title: "Coordinator Institutional Planning, Research, & Publications",
                                    image: "{{ asset('images/profiles/staff1.png') }}"
                                }
                            ]
                        },
                        {
                            id: "database",
                            name: "Engr. Maria Elena Y. Timbang",
                            title: "Head, Institutional Database Management Office",
                            image: "{{ asset('images/profiles/database.png') }}"
                        },
                        {
                            id: "document",
                            name: "Ms. Corazon Q. Mallari",
                            title: "Institutional Document Controller",
                            image: "{{ asset('images/profiles/document.png') }}",
                            children: [
                                {
                                    id: "staff2",
                                    name: "Ms. Angela Joy D. Villar",
                                    title: "Staff, Institutional Document Controller",
                                    image: "{{ asset('images/profiles/staff2.png') }}"
                                }
                            ]
                        }
                    ]
                }
            ]
        };

        //Constants
        const AVATAR_R      = 48;
        const AVATAR_BORDER = 3;
        const AVATAR_ABOVE  = AVATAR_R + AVATAR_BORDER;
        const AVATAR_CY     = -10;    
        const NODE_W        = 210;
        const NODE_H        = 130;
        const H_GAP         = 48;
        const V_GAP         = 120;
        const V_GAP_L2      = 110;
        const LINE_W        = 3;

        function render() {
            d3.select('#orgchart').selectAll('*').remove();

            const root = d3.hierarchy(DATA);

            const BASE_STEP = NODE_H + V_GAP;
            const EXTRA_Y   = V_GAP_L2 - V_GAP;

            d3.tree()
                .nodeSize([NODE_W + H_GAP, BASE_STEP])
                .separation((a, b) => a.parent === b.parent ? 1 : 1.3)
                (root);

            root.each(d => { if (d.depth >= 3) d.y += EXTRA_Y; });

            let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
            root.each(d => {
                minX = Math.min(minX, d.x - NODE_W / 2);
                maxX = Math.max(maxX, d.x + NODE_W / 2);
                minY = Math.min(minY, d.y - AVATAR_ABOVE);
                maxY = Math.max(maxY, d.y + NODE_H);
            });

            const PAD  = 48;
            const vbW  = (maxX - minX) + PAD * 2;
            const vbH  = (maxY - minY) + PAD * 2;
            const offX = -minX + PAD;
            const offY = -minY + PAD;

            const svg = d3.select('#orgchart')
                .append('svg')
                .attr('viewBox', `0 0 ${vbW} ${vbH}`)
                .attr('preserveAspectRatio', 'xMidYMid meet')
                .attr('width', '100%')
                .attr('height', vbH);

            const defs = svg.append('defs');

            const shadow = defs.append('filter')
                .attr('id', 'card-shadow')
                .attr('x', '-15%').attr('y', '-15%')
                .attr('width', '130%').attr('height', '145%');
            shadow.append('feDropShadow')
                .attr('dx', 0).attr('dy', 5)
                .attr('stdDeviation', 7)
                .attr('flood-color', 'rgba(0,0,0,0.32)');

            const presGrad = defs.append('linearGradient')
                .attr('id', 'grad-president')
                .attr('x1', '0%').attr('y1', '0%')
                .attr('x2', '0%').attr('y2', '100%');
            presGrad.append('stop').attr('offset', '0%').attr('stop-color', '#9a1535');
            presGrad.append('stop').attr('offset', '100%').attr('stop-color', '#5c0d20');

            const staffGrad = defs.append('linearGradient')
                .attr('id', 'grad-staff')
                .attr('x1', '0%').attr('y1', '0%')
                .attr('x2', '0%').attr('y2', '100%');
            staffGrad.append('stop').attr('offset', '0%').attr('stop-color', '#3a2e1a');
            staffGrad.append('stop').attr('offset', '100%').attr('stop-color', '#1e1a0e');

            root.each(d => {
                defs.append('clipPath')
                    .attr('id', `clip-${d.data.id}`)
                    .append('circle')
                    .attr('cx', 0).attr('cy', AVATAR_CY).attr('r', AVATAR_R);
            });

            // Main group
            const g = svg.append('g')
                .attr('transform', `translate(${offX}, ${offY})`);

            // Line connectors
            g.selectAll('.link')
                .data(root.links())
                .enter()
                .append('path')
                .attr('class', 'link')
                .attr('fill', 'none')
                .attr('stroke', '#9a8a5a')
                .attr('stroke-width', LINE_W)
                .attr('shape-rendering', 'crispEdges')
                .attr('d', d => {
                    const sx  = d.source.x;
                    const sy  = d.source.y + NODE_H;
                    const tx  = d.target.x;
                    const ty  = d.target.y - AVATAR_ABOVE;
                    const mid = sy + (ty - sy) / 2;
                    return `M ${sx} ${sy} L ${sx} ${mid} L ${tx} ${mid} L ${tx} ${ty}`;
                });

            // Node groups
            const node = g.selectAll('.node')
                .data(root.descendants())
                .enter()
                .append('g')
                .attr('class', d => `node node--${d.data.id}`)
                .attr('transform', d => `translate(${d.x}, ${d.y})`);

            // Main card 
            node.append('rect')
                .attr('class', 'card')
                .attr('x', -NODE_W / 2).attr('y', 0)
                .attr('width', NODE_W).attr('height', NODE_H)
                .attr('rx', 11)
                .attr('fill', d => d.data.id === 'president'
                    ? 'url(#grad-president)' : 'url(#grad-staff)')
                .attr('stroke', '#c9a84c')
                .attr('stroke-width', LINE_W)
                .attr('filter', 'url(#card-shadow)');

            // Gold top accent bar 
            node.append('rect')
                .attr('x', -NODE_W / 2).attr('y', 0)
                .attr('width', NODE_W).attr('height', 5)
                .attr('rx', 11).attr('fill', '#c9a84c');
            node.append('rect')
                .attr('x', -NODE_W / 2).attr('y', 3)
                .attr('width', NODE_W).attr('height', 4)
                .attr('fill', '#c9a84c');

            // Avatar background circle 
            node.append('circle')
                .attr('cx', 0).attr('cy', AVATAR_CY)
                .attr('r', AVATAR_R + 2)
                .attr('fill', d => d.data.id === 'president' ? '#7a1030' : '#2a2010')
                .attr('stroke', '#c9a84c')
                .attr('stroke-width', LINE_W)
                .attr('filter', d => d.data.id === 'president' ? 'url(#avatar-glow)' : null);

            // Avatar image 
            node.append('image')
                .attr('x', -AVATAR_R)
                .attr('y', AVATAR_CY - AVATAR_R)
                .attr('width', AVATAR_R * 2).attr('height', AVATAR_R * 2)
                .attr('clip-path', d => `url(#clip-${d.data.id})`)
                .attr('href', d => d.data.image)
                .attr('preserveAspectRatio', 'xMidYMid slice')
                .on('error', function (event, d) {
                    d3.select(this).style('display', 'none');
                    const parts    = d.data.name.split(' ');
                    const initials = (parts[1]?.[0] ?? parts[0][0]) + parts[parts.length - 1][0];
                    d3.select(this.parentNode).select('.avatar-initials')
                        .style('display', null).text(initials);
                });

            // Initials
            node.append('text')
                .attr('class', 'avatar-initials')
                .attr('x', 0).attr('y', AVATAR_CY + 7)
                .attr('text-anchor', 'middle')
                .attr('font-family', "'Playfair Display', Georgia, serif")
                .attr('font-size', 20).attr('font-weight', '700')
                .attr('fill', '#c9a84c')
                .style('display', 'none')
                .text(d => {
                    const p = d.data.name.split(' ');
                    return (p[1]?.[0] ?? p[0][0]) + p[p.length - 1][0];
                });

            // Name text
            node.each(function (d) {
                const text = d3.select(this).append('text')
                    .attr('text-anchor', 'middle')
                    .attr('font-family', "'Helvetica Neue', ")
                    .attr('font-size', d.data.id === 'president' ? 12.5 : 11.5)
                    .attr('font-weight', '700')
                    .attr('fill', '#f5e8c8')
                    .attr('letter-spacing', '0.02em');
                wrapText(text, d.data.name, NODE_W - 22, AVATAR_R + 14, 14.5);
            });

            // Title text 
            node.each(function (d) {
                const nameLines = d3.select(this).selectAll('text')
                    .filter((_, i) => i === 0).selectAll('tspan').size() || 1;
                const titleY = AVATAR_R + 14 + nameLines * 14.5 + 5;
                const text = d3.select(this).append('text')
                    .attr('text-anchor', 'middle')
                    .attr('font-family', "'Lato', 'Helvetica Neue', sans-serif")
                    .attr('font-size', 10)
                    .attr('fill', '#d4b86a')
                    .attr('letter-spacing', '0.01em');
                wrapText(text, d.data.title, NODE_W - 26, titleY, 12);
            });

        }

        // Text wrap helper 
        function wrapText(text, str, maxW, startY, lineH) {
            const maxChars = Math.floor(maxW / 6.2);
            const words    = str.split(' ');
            let line       = [], lineIndex = 0;
            words.forEach((word, wi) => {
                line.push(word);
                if (line.join(' ').length >= maxChars || wi === words.length - 1) {
                    text.append('tspan')
                        .attr('x', 0)
                        .attr('y', startY + lineIndex * lineH)
                        .text(line.join(' '));
                    line = [];
                    lineIndex++;
                }
            });
        }

        // Debounced resize 
        let t;
        window.addEventListener('resize', () => { clearTimeout(t); t = setTimeout(render, 150); });

        render();
    })();
    });
    </script>
    @endpush

    @push('head')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@400;700&display=swap');

        .oie-chart-wrap {
            padding-top: 60px;
            min-height: 520px;
            overflow-x: auto;
        }
        #orgchart {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }
        .node { cursor: pointer; }
        .node text, .node image { pointer-events: none; user-select: none; }
        .link { stroke-linecap: square; }
        #orgchart svg text {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
    @endpush

@endsection