# Floor Management Guide

## How to Manage Floors in Buildings

### Viewing Floors

1. **Navigate to Buildings**
   - Click "Buildings" in the left sidebar menu
   - Or go to: `http://127.0.0.1:8000/real-estate/buildings`

2. **Select a Building**
   - Click on any building card to view its details
   - Or go to: `http://127.0.0.1:8000/real-estate/buildings/{id}`

3. **View Floors Section**
   - Scroll down to the "Floors & Units" section
   - You'll see all floors listed with:
     - Floor number (e.g., "Floor 1", "Floor 2")
     - Number of units on that floor
     - "View Units" button - Opens detailed floor view
     - "Edit" button - Edit floor details

---

## Managing Floors

### Adding a New Floor

**Option 1: From Building Page**
1. Go to the building detail page
2. In the "Floors & Units" section, click the **"Add Floor"** button (top right)
3. The building will be automatically pre-selected
4. Enter floor details:
   - Floor Number (e.g., 1, 2, 3)
   - Total Units
   - Description (optional)
   - Floor Plan Layout (optional)
5. Click "Create Floor"

**Option 2: Direct URL**
- Go to: `http://127.0.0.1:8000/real-estate/floors/create?building_id={building_id}`
- Building will be pre-selected automatically

---

### Editing a Floor

**From Building Page:**
1. Go to the building detail page
2. Find the floor you want to edit in the "Floors & Units" section
3. Click the **"Edit"** button next to that floor
4. Update the floor details
5. Click "Update Floor"

**From Floor Detail Page:**
1. Click "View Units" on any floor
2. Click the "Edit" button at the top
3. Update the floor details
4. Click "Update Floor"

---

### Viewing Floor Details

1. Go to the building detail page
2. Find the floor in the "Floors & Units" section
3. Click the **"View Units"** button
4. You'll see:
   - Floor information
   - All units on that floor
   - Unit statistics
   - Options to add/edit units

---

### Deleting a Floor

1. Go to the floor detail page (click "View Units")
2. Scroll to the bottom
3. Click the "Delete Floor" button
4. Confirm the deletion
5. **Note:** You can only delete floors that have no units

---

## Floor Display on Building Page

### What You See:

```
┌─────────────────────────────────────────────┐
│ Floors & Units              [+ Add Floor]   │
├─────────────────────────────────────────────┤
│                                             │
│ Floor 1                    [View] [Edit]    │
│ ┌────┐ ┌────┐ ┌────┐ ┌────┐               │
│ │101 │ │102 │ │103 │ │104 │               │
│ └────┘ └────┘ └────┘ └────┘               │
│                                             │
│ ─────────────────────────────────────────  │
│                                             │
│ Floor 2                    [View] [Edit]    │
│ ┌────┐ ┌────┐ ┌────┐ ┌────┐               │
│ │201 │ │202 │ │203 │ │204 │               │
│ └────┘ └────┘ └────┘ └────┘               │
│                                             │
└─────────────────────────────────────────────┘
```

### Unit Tile Colors:
- 🟢 **Green** - Available
- 🔵 **Blue** - Rented
- 🟡 **Yellow** - Reserved
- 🔴 **Red** - Maintenance

---

## Quick Actions Available

### From Building Page:
- ✅ Add Floor
- ✅ View Floor Details
- ✅ Edit Floor
- ✅ Add Units to Floor
- ✅ View Unit Details (click on unit tile)

### From Floor Detail Page:
- ✅ Edit Floor
- ✅ Delete Floor
- ✅ Add Units
- ✅ View Building
- ✅ Bulk Create Units

---

## Navigation Flow

```
Buildings List
    ↓
Select Building
    ↓
Building Detail Page
    ↓ (scroll down)
Floors & Units Section
    ↓
[Add Floor] or [View Units] or [Edit]
    ↓
Floor Management
```

---

## Important Notes

1. **No Separate Floors Page**: Floors are managed within each building's context
2. **Auto-Selection**: When creating a floor from a building page, the building is automatically selected
3. **Breadcrumbs**: Navigation breadcrumbs show: Buildings > [Building Name] > Floor X
4. **Back Buttons**: All "Back" buttons return to the building detail page
5. **Deletion**: Floors can only be deleted if they have no units

---

## Example URLs

- **Buildings List**: `http://127.0.0.1:8000/real-estate/buildings`
- **Building Detail**: `http://127.0.0.1:8000/real-estate/buildings/1`
- **Add Floor**: `http://127.0.0.1:8000/real-estate/floors/create?building_id=1`
- **Edit Floor**: `http://127.0.0.1:8000/real-estate/floors/1/edit`
- **View Floor**: `http://127.0.0.1:8000/real-estate/floors/1`

---

## Troubleshooting

**Q: I don't see any floors**
- A: Make sure you've added floors to the building. Click "Add Floor" button.

**Q: Where is the Floors menu item?**
- A: It has been removed. Floors are now managed within each building.

**Q: How do I see all floors across all buildings?**
- A: This feature has been removed. View floors by selecting individual buildings.

**Q: The building field is asking me to select a building**
- A: Make sure you're accessing the create floor page with `?building_id=X` parameter, or it will show a dropdown.

---

## Best Practices

1. **Create floors first** before adding units
2. **Use sequential floor numbers** (0, 1, 2, 3...) for ground and upper floors
3. **Add descriptions** to floors for better organization
4. **Use floor plan layouts** to categorize unit types on each floor
5. **Bulk create units** when you have many similar units on a floor
