/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import java.util.Date;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;
import org.codehaus.jackson.annotate.JsonIgnore;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "set_info")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "SetInfo.findAll", query = "SELECT s FROM SetInfo s"),
    @NamedQuery(name = "SetInfo.findBySetId", query = "SELECT s FROM SetInfo s WHERE s.setId = :setId"),
    @NamedQuery(name = "SetInfo.findBySetName", query = "SELECT s FROM SetInfo s WHERE s.setName = :setName"),
    @NamedQuery(name = "SetInfo.findByTs", query = "SELECT s FROM SetInfo s WHERE s.ts = :ts")})
public class SetInfo implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "set_id")
    private Long setId;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 45)
    @Column(name = "set_name")
    private String setName;
    @Basic(optional = false)
    @NotNull
    @Column(name = "TS")
    @Temporal(TemporalType.TIMESTAMP)
    private Date ts;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "setTarId")
    private Collection<AnnoToSet> annoToSetCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "setSrcId")
    private Collection<SetContainObj> setContainObjCollection;
    @JoinColumn(name = "user_id", referencedColumnName = "user_id")
    @ManyToOne(optional = false)
    private User userId;

    public SetInfo() {
    }

    public SetInfo(Long setId) {
        this.setId = setId;
    }

    public SetInfo(Long setId, String setName, Date ts) {
        this.setId = setId;
        this.setName = setName;
        this.ts = ts;
    }

    public Long getSetId() {
        return setId;
    }

    public void setSetId(Long setId) {
        this.setId = setId;
    }

    public String getSetName() {
        return setName;
    }

    public void setSetName(String setName) {
        this.setName = setName;
    }

    public Date getTs() {
        return ts;
    }

    public void setTs(Date ts) {
        this.ts = ts;
    }

    @XmlTransient     @JsonIgnore
    public Collection<AnnoToSet> getAnnoToSetCollection() {
        return annoToSetCollection;
    }

    public void setAnnoToSetCollection(Collection<AnnoToSet> annoToSetCollection) {
        this.annoToSetCollection = annoToSetCollection;
    }

    @XmlTransient     @JsonIgnore
    public Collection<SetContainObj> getSetContainObjCollection() {
        return setContainObjCollection;
    }

    public void setSetContainObjCollection(Collection<SetContainObj> setContainObjCollection) {
        this.setContainObjCollection = setContainObjCollection;
    }

    public User getUserId() {
        return userId;
    }

    public void setUserId(User userId) {
        this.userId = userId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (setId != null ? setId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof SetInfo)) {
            return false;
        }
        SetInfo other = (SetInfo) object;
        if ((this.setId == null && other.setId != null) || (this.setId != null && !this.setId.equals(other.setId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.SetInfo[ setId=" + setId + " ]";
    }
    
}
